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

		
		//Log Payment To Table
		$verify = WebhookResponse::firstWhere("exchanger_reference", $paymentReference);
		if($verify){
			return response()->json(["status"=>"Transaction Treated"], 300);
		}
		WebhookResponse::create([
			"amount" => $amountPaid,
    		"userid" => $accountReference,
    		"response" => $request,
    		"method" => $paymentDescription,
    		"paidon"=> $paidOn,
    		"status"=> $paymentStatus,
    		"exchanger_reference" => $paymentReference
		]);

		//Get User with Account Details
		$userD = User::where("user_account", $accountNumber)->get();
		$user = $userD[0];

		$wallet = Wallet::where("userid", $user->userid)->get();

		Wallet::where("userid", $user->userid)->update(["wallet_balance"=>$amountPaid + $wallet[0]['wallet_balance'] ]);
		return response()->json(["status"=>"successful"], 200);
		
    }

}
