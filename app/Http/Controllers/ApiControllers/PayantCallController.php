<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PayantCallController extends Controller
{



	private function payantLogin(){

	    $endpoint = "oauth/token";
	    $data = array(
	                "username"=>config('app.PYT_USERNAME'),
	                "password"=>config('app.PYT_PASSWORD'));
	    $bankurl = config('app.PYT_BANK_BASEURL');
	    $content = json_encode($data);
	    
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $bankurl.$endpoint);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    $response = curl_exec($ch);
	    curl_close($ch);
	    $out = json_decode($response, false);

	    $hashKey = $out->data->token;
	   
	    return $hashKey;
	}

	public function confirmTransactionPayant($ref){
	    $hash = $this->payantLogin();
	    $organizationId = config('app.PYT_ORGANIZATIONID');

	    $endpoint = "accounts/transactions/".$ref;
	   
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $bankurl.$endpoint);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer {$hash}","Content-Type: application/json", "OrganizationID: {$organizationId}"));
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    $response = curl_exec($ch);
	    curl_close($ch);
	    $status = json_decode($response, FALSE);
	    if($status->statusCode == 200){
	        return TRUE;
	    }
	    else{
	        return FALSE;
	    }
	}

	//To create Account Number for Payant

	public function reserveAccountPayant($details){
	    $url = "http://api.airvendng.net/payantConnect/reserveAccount/"; //Your Serverside url
	    $appID = "004"; //ID set on ra_vendors
	    $hashKey="CPL88F1B23F1A2BFD9234425F59390A9C58AEFDA6336983E982F79DF71C1BA5676B"; //HASH set on ra_Vendor
	    
	    $content =  json_encode($details);

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array("hashKey: $hashKey","privateID: $appID"));
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    $output = curl_exec($ch);
	    curl_close($ch);

	    return $output;
	}

	//Card Payment. White Labelled.
	private function paymentCall($payload, $endpoint){
		$pyt = config('app.PYT_PRIVATEKEY');
		 $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, config('app.PYT_CARD_BASEURL').$endpoint);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer {$pyt}","Content-Type: application/json"));
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    $response = curl_exec($ch);
	    curl_close($ch);
	    return $response;
	}

	public function createInvoice($data,$tokenize,$amount)
	{	

		$date = date('d/m/Y', strtotime(' +1 year'));
		$content = array(
				"due_date"=> $date,
				"fee_bearer"=> "client",
				"tokenize"=>"$tokenize",
				"payment_method"=>"card",
				"metadata"=>array(
								"payment"=>"Card Funding for Welfare"),
				"items"=>array(
							array("item"=>"Payment for Welfare",
							"description"=>"Contribution For Account",
							"unit_cost"=>$amount,
							"quantity"=>"1")
					),
				"client"=>$data
				);

		$content = json_encode($content);

		

		$resp = $this->paymentCall($content, "invoices");

		return json_decode($resp, false);

	}

	public function sendCard($ref, $details)
	{
		$content = ["reference_code"=>$ref, "card"=>$details];
		$content = json_encode($content);
		$resp = $this->paymentCall($content, "pay/sdk/card");

		return json_decode($resp);
	}


	public function confirmPayment($ref)
	{
		$resp = $this->paymentCall($content, "payments/$ref");

		return json_decode($resp);
	}

	public function createTokenPayment($client_id, $token, $amount)
	{
		$content =  array(
						"client_id"=> $client_id,
						"due_date"=> $date,
						"fee_bearer"=> "client",
						"merchant_ref"=> "NA".uniqid(),
						"card_token"=>$token,
						"items"=>array(
							array("item"=>"Payment for Welfare",
								"description"=>"Recurring Payment for Welfare",
								"unit_cost"=>$amount,
								"quantity"=>"1")
						)
					);
		$content = json_encode($content);

		$resp = $this->paymentCall($content, "invoices");

		return json_decode($resp);
		
	}
}
