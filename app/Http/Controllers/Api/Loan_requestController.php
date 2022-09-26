<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Loan_request;
use App\Models\Loan_repayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; 
use Validator;

class Loan_requestController extends Controller
{
    /**
     * Create loan request on customer application
     */
    public function create(Request $request)
    {
        try {
            //validate the form request
            $validator = Validator::make($request->all(), [ 
                'amount' => 'required|integer',
                'loan_tenor' => 'required|integer'
            ]);
            if ($validator->fails()) { 
                return response()->json(['error'=>$validator->errors()], config('global.status.validation'));            
            }
            
            // Add loan request date into loan_request table
            $add_loan_request = new Loan_request;
            $add_loan_request->user_id = Auth::id();
            $add_loan_request->amount = $request->amount;
            $add_loan_request->loan_tenor = $request->loan_tenor;
            $add_loan_request->loan_status = Loan_request::LOAN_STATUS_PENDING;

            if($add_loan_request->save()){
                return response()->json(['success'=>$add_loan_request], config('global.status.success')); 
            }else{
                return response()->json(['error'=>config('global.message.not_added')], config('global.status.bad_request'));
            }
        } catch (\Exception $e) {
            return response()->json(['error'=>config('global.message.error')], config('global.status.bad_request'));
        }
        
    }

    /**
     * Get loan list user wise
     */
    public function getUserLoan($id=null)
    {
        // if we send id then it will search for that particular user loan else go for loggedin user loan
        if($id == null){
            $loan_user_id = Auth::id();
        }else{
            $loan_user_id = $id;
        }
        //Get loan list user wise
        $get_loan = Loan_request::where('user_id',$loan_user_id)->get();
        if($get_loan && count($get_loan)>0){
            return response()->json(['success'=>$get_loan], config('global.status.success'));
        }else{
            return response()->json(['success'=>config('global.message.no_data_found')], config('global.status.success'));
        }
        
    }

    /**
     * Get all loan list
     */
    public function getAllLoan()
    {
        //Get loan list user wise
        $get_loan = Loan_request::get();
        if($get_loan && count($get_loan)>0){
            return response()->json(['success'=>$get_loan], config('global.status.success'));
        }else{
            return response()->json(['success'=>config('global.message.no_data_found')], config('global.status.success'));
        }
    }

    /**
     * Update loan status
     */
    public function updateLoanStatus(Request $request)
    {
        try {

            //validate the form request
            $validator = Validator::make($request->all(), [ 
                'id' => 'required|integer',
                'loan_status' => 'required|integer|between:1,4'
            ]);
            if ($validator->fails()) { 
                return response()->json(['error'=>$validator->errors()], config('global.status.validation'));            
            }
           //Get loan list user wise
            $get_loan = Loan_request::find($request->id);
            $old_loan_status = $get_loan->loan_status; // store existing loan status
            $get_loan->loan_status = $request->loan_status;            
            if($get_loan->save()){
                
                if($old_loan_status == Loan_request::LOAN_STATUS_PENDING && $request->loan_status == Loan_request::LOAN_STATUS_APPROVED){
                    $genrate_emi_amount = $get_loan->amount/$get_loan->loan_tenor; // Generate emi amount for loan tenor
                    for($i = 1; $i<=$get_loan->loan_tenor;$i++){
                        //Create emi tenure date
                        $date = Carbon::create($get_loan->created_at);
                        $daysToAdd = $i*config('global.loan_term'); //add loan term days to loan applied date 
                        $date = $date->addDays($daysToAdd);
                        //add emi record for loan repayment                    
                        $add_record = new Loan_repayment;
                        $add_record->loan_id = $request->id;
                        $add_record->repayment_amount = $genrate_emi_amount;
                        $add_record->emi_date = $date;
                        $add_record->loan_status = Loan_repayment::LOAN_REPAYMENT_STATUS_PENDING;
                        $add_record->save();
                    }
                }               
                
                return response()->json(['success'=>$get_loan], config('global.status.success')); 
            }else{
                return response()->json(['error'=>config('global.message.not_updated')], config('global.status.bad_request'));
            }
        } catch (\Exception $e) {
            return response()->json(['error'=>config('global.message.error')], config('global.status.bad_request'));
        }        
    }
    
    /**
     * Update loan repayment status
     */
    public function updateLoanRepaymentStatus(Request $request)
    {
        try {

            //validate the form request
            $validator = Validator::make($request->all(), [ 
                'loan_id' => 'required|integer',
                'loan_status' => 'required|integer|between:1,2'
            ]);
            if ($validator->fails()) { 
                return response()->json(['error'=>$validator->errors()], config('global.status.validation'));            
            }
            // Check if requested loan is aprroved or not
            $get_loan_status = Loan_request::find($request->loan_id);
            if($get_loan_status->loan_status == Loan_request::LOAN_STATUS_APPROVED){
                //Get first loan repayment
                $get_loan = Loan_repayment::where('loan_status','!=',Loan_repayment::LOAN_REPAYMENT_STATUS_PAID)->where('loan_id',$request->loan_id)->first();
                
                $old_loan_status = $get_loan->loan_status; // store existing loan status
                $get_loan->loan_status = $request->loan_status;            
                if($get_loan->save()){
                    // Check all loan repayments paid or not
                    $pending_loan_repayment_count = Loan_repayment::where('loan_status',Loan_repayment::LOAN_REPAYMENT_STATUS_PENDING)->where('loan_id',$request->loan_id)->count();

                    // if no pending repayment found then update loan status as 0
                    if($pending_loan_repayment_count == 0){
                        $loan_request_update = Loan_request::find($request->loan_id);
                        $loan_request_update->loan_status = Loan_request::LOAN_STATUS_PAID;
                        $loan_request_update->save();
                    }                                
                    
                    return response()->json(['success'=>$get_loan], config('global.status.success')); 
                }else{
                    return response()->json(['error'=>config('global.message.not_added')], config('global.status.bad_request'));
                }
            }elseif($get_loan_status->loan_status == Loan_request::LOAN_STATUS_PAID){
                return response()->json(['error'=>config('global.message.loan_paid')], config('global.status.bad_request'));
            }
            else{
                return response()->json(['error'=>config('global.message.loan_not_approved')], config('global.status.bad_request'));
            }
            
        } catch (\Exception $e) {
            return response()->json(['error'=>config('global.message.error')], config('global.status.bad_request'));
        }        
    } 
}
