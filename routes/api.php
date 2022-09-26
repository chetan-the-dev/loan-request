<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\Loan_requestController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [UserController::class, 'login']);
Route::post('register', [UserController::class, 'register']);
 
Route::group(['middleware' => 'auth:api'], function(){
    Route::post('/loans/create', [Loan_requestController::class,'create']);
    Route::get('/user/loans', [Loan_requestController::class,'getUserLoan']);
    Route::post('/user/loan/repayment', [Loan_requestController::class,'updateLoanRepaymentStatus']);
});

Route::group(['middleware' => 'auth:api','verifyAdmin'], function(){
    Route::get('/loans/all', [Loan_requestController::class,'getAllLoan']);
    Route::post('/loans/update', [Loan_requestController::class,'updateLoanStatus']);
});
