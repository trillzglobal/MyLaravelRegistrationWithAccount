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
    	
    	$data = $request->getContent();
    	$data = json_decode($data, true);
    	
		$paymentReference = $data['transactionReference'];
		$amountPaid = $data['amountPaid'];
		$paidOn = $data['date'];
		$paymentStatus ='1';
		$accountReference = $data['customerReference'];
		$accountNumber = $data['accountNumber'];
		$paymentDescription = 'Direct Account';

		
		
		//Log Payment To Table
		$verify = WebhookResponse::firstWhere("exchanger_reference", $paymentReference);
		if($verify){
			return response()->json(["status"=>"Transaction Treated"], 300);
		}

		$user = User::where("user_account", $accountNumber)->first();


		WebhookResponse::create([
			"amount" => $amountPaid,
    		"userid" => $user->userid,
    		"email"=> $user->email,
    		"response" => $request->getContent(),
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
