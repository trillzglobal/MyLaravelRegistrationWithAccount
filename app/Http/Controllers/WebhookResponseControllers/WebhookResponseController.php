<?php

namespace App\Http\Controllers\WebhookResponseControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WebhookResponse;


class WebhookResponseController extends Controller
{
    //

    //Direct Wallet Funding Webhook
    public function connectPayant(Request $request)
    {

		$paymentReference = $request->paymentReference;
		$amountPaid = $request->amount;
		$paidOn = $request->createdAt;
		$paymentStatus = $request->input('status', '1');
		$accountReference = $request->input('account.customer._id');
		$accountNumber = $request->input('account.accountNumber');
		$paymentDescription = $request->input('narration', 'Direct Account');

		WebhookResponse::create([
			"amount" => $amountPaid,
    		"userid" => $accountReference,
    		"response" => $request,
    		"method" => $paymentDescription,
    		"paidon"=> $paidOn,
    		"status"=> $paymentStatus,
    		"exchanger_reference" => $paymentReference
		]);

		return response()->json(["status"=>"successful"], 200);
    }
}
