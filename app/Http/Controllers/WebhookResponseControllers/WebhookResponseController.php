<?php

namespace App\Http\Controllers\WebhookResponseControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WebhookResponse;
use App\Models\User;
use App\Models\Wallet;


class WebhookResponseController extends Controller
{
    //

    //Direct Wallet Funding Webhook
    public function connectPayant(Request $request)
    {
    	
    	
		$paymentReference = $request->transactionReference;
		$amountPaid = $request->amountPaid;
		$paidOn = $request->date;
		$paymentStatus ='1';
		$accountReference = $request->customerReference;
		$accountNumber = $request->accountNumber;
		$paymentDescription = 'Direct Account';

		print_r($accountNumber);
		
		//Log Payment To Table
		$verify = WebhookResponse::firstWhere("exchanger_reference", $paymentReference);
		if($verify){
			return response()->json(["status"=>"Transaction Treated"], 300);
		}

		$user = User::where("user_account", $accountNumber)->first();

		print_r($user);
		exit();
		WebhookResponse::create([
			"amount" => $amountPaid,
    		"userid" => $user->userid,
    		"response" => $request,
    		"method" => $paymentDescription,
    		"paidon"=> $paidOn,
    		"status"=> $paymentStatus,
    		"exchanger_reference" => $paymentReference
		]);

		//Get User with Account Details
		


		$wallet = Wallet::where("userid", $user->userid)->first();
		$amt = $amountPaid + $wallet->wallet_balance; 

		Wallet::where("userid", $user->userid)->update(["wallet_balance"=>$amt]);
		return response()->json(["status"=>"successful"], 200);
		
    }

}
