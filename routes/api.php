<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/direct/wallet/funding', [App\Http\Controllers\WebhookResponseControllers\WebhookResponseController::class, 'connectPayant'])->name('/direct/wallet/funding'); //Done Direct Wallet Funding Callback


Route::prefix('auth')->group(function(){
	Route::post('signup/user', [App\Http\Controllers\ApiAuthController::class, 'signup']);
	Route::post('login/user', [App\Http\Controllers\ApiAuthController::class, 'login']);


});


Route::group([
        'middleware'=>'auth:api'
    ], function(){

    	Route::post('logout', [App\Http\Controllers\ApiAuthController::class, 'logout']);
    	Route::get('pin/details', [App\Http\Controllers\TransactionControllers\UserInformation::class, 'pinAllowed']); //Done List of available PINs and Prices
    	Route::get('vendor/transaction', [App\Http\Controllers\TransactionControllers\UserInformation::class, 'pinGenerationHistory']); //Done {History of Pin Generated by Customer}
    	Route::get('users/transaction', [App\Http\Controllers\TransactionControllers\UserInformation::class, 'transactionHistory']); //Done {History of Transactions by Customer's Users}
    	Route::post('pin/purchase', [App\Http\Controllers\TransactionControllers\TransactionPinController::class, 'purchasePin']);  //Done {Purchase PIN}
    	Route::get('pin/search/{ref_id}', [App\Http\Controllers\TransactionControllers\TransactionPinController::class, 'pinSearch']); //Done {Include Details of PINs in a PIN ref log}
    	Route::get('all/purchase', [App\Http\Controllers\TransactionControllers\UserInformation::class, 'allpurchase']); //Not Done {Include all Pin Purchased By Customer}
    	Route::get('transaction/graph', [App\Http\Controllers\TransactionControllers\UserInformation::class, 'transactionSummary']); //Not Done {Include summay of Purchase By Customer}
    	Route::get('users/incentives', [App\Http\Controllers\TransactionControllers\UserInformation::class, 'incentive']); //Not Done {Includes a list of Bonus Earn By user}

    });

Route::any('/', [App\Http\Controllers\ApiAuthController::class, 'index']);
Route::any('/api/forgot/password', [App\Http\Controllers\ApiAuthController::class, 'forgot']);
Route::any('/reset/password', [App\Http\Controllers\ApiAuthController::class, 'reset']);