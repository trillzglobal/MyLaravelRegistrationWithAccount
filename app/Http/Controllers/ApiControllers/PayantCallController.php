<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PayantCallController extends Controller
{



	private function payantLogin(){

	    $endpoint = "oauth/token";
	    $data = array(
	                "username"=>env('PYT_USERNAME'),
	                "password"=>env('PYT_PASSWORD'));
	    $bankurl = env('PYT_BANK_BASEURL');
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

	    //$hashKey = $out->data->token;
	    print_r($bankurl." ".$data['username']." ".$endpoint);
	    exit();
	    return $hashKey;
	}

	public function confirmTransactionPayant($ref){
	    $hash = $this->payantLogin();
	    $organizationId = env('pyt_organizationId');

	    $endpoint = "accounts/transactions/".$ref;
	   
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $_ENV['PYT_BANK_BASEURL'].$endpoint);
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
	    $hash = $this->payantLogin();
	    $organizationId = $_ENV['PYT_ORGANIZATIONID'];
	    $endpoint = "accounts";
	    $details['country'] = $_ENV['PYT_COUNTRY'];
	    $details['currency'] = $_ENV['PYT_CURRENCY'];
	    $details['type'] = "RESERVED";
	    $details['bankCode'] = $_ENV['PYT_BANKCODE'];
	    $details['accountName'] = $details["customer"]["name"];
	   
	    $content = json_encode($details);


	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $_ENV['PYT_BANK_BASEURL'].$endpoint);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer {$hash}","Content-Type: application/json", "OrganizationID: {$organizationId}"));
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    $response = curl_exec($ch);
	    curl_close($ch);
	    return $response;
	}

	//Card Payment. White Labelled.
	private function paymentCall($payload, $endpoint){
		$pyt = $_ENV['PYT_PRIVATEKEY'];
		 $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $_ENV['PYT_CARD_BASEURL'].$endpoint);
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
