<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; 
use Illuminate\Support\Facades\Auth; 
use Validator;

class UserController extends Controller
{
    /** 
     * login api
     */ 
    public function login(){
        try { 
            if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
                $user = Auth::user(); 
                $success['token'] =  $user->createToken('MyLaravelApp')-> accessToken; 
                $success['userId'] = $user->id;
                return response()->json(['success' => $success], config('global.status.success')); 
            } 
            else{ 
                return response()->json(['error'=>config('global.message.invalid_data')], config('global.status.unauthorized')); 
            }
        } catch (\Exception $e) {
            return response()->json(['error'=>config('global.message.error')], config('global.status.bad_request'));
        } 
    }
 
    /** 
     * Register api 
     */ 
    public function register(Request $request) 
    {
        try {
            $validator = Validator::make($request->all(), [ 
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required',
                'c_password' => 'required|same:password',
                'user_role' => 'required|between:1,2',
            ]);
            if ($validator->fails()) { 
                return response()->json(['error'=>$validator->errors()], config('global.status.validation'));            
            }
            $input = $request->all(); 
            $input['password'] = bcrypt($input['password']); 
            $user = User::create($input); 
            $success['token'] =  $user->createToken('MyLaravelApp')-> accessToken; 
            $success['name'] =  $user->name;
            return response()->json(['success'=>$success], config('global.status.success'));
        } catch (\Exception $e) {
            return response()->json(['error'=>config('global.message.error')], config('global.status.bad_request'));
        }         
    }
}
