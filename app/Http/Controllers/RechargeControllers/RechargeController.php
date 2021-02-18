<?php

namespace App\Http\Controllers\RechargeControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PinDataInfo;
use App\Models\TransactionTable;
use App\Models\RequestTable;
use App\Http\Controllers\ApiControllers\ApiController;
use App\Http\Controllers\CalculatorControllers\CalculatorController;


class RechargeController extends Controller
{
    //

    private function responseConstruct($msisdn,$sessionid,$ussdtext,$end='0')
    {
    	$output = '<?xml version="1.0" encoding="UTF-8"?>
					<output>
				 <msisdn>'.$msisdn.'</msisdn>
				 <sess>'.$sessionid.'</sess>
				 <msgid>'.$sessionid.'</msgid>
				 <text>'.$ussdtext.'</text>
				 <endsess>'.$end.'</endsess>
				 </output>';

		return $output;
    }


	public function Recharge(Request $request)
	{
		$msisdn = $request->msisdn;
		$mno = $request->mno;
		$input = $request->msg;
		$sessionid = $request->sessionid;

		//Store Server Request By User
		$request = new RequestTable;

		
		$request->msisdn = $msisdn;
		$request->mno = $mno;
		$request->ussd_data = $input;
		$request->sessionid = $sessionid;
		$request->save();

		//Validate if USSD is upto Required Number of String
		if(strlen($input) != 16 && strlen($input) != 12){

			$ussdtext = "Airvend 174\n\n";
			$ussdtext .= "Pin Lenght is Incorrect\n";
			$ussdtext .= "Contact: info@callphoneng.com";
			$end = 1;

			$response = $this->responseConstruct($msisdn,$sessionid,$ussdtext,$end);
			return response($response)->header('Content-Type', 'application/xml');
		}

		//Call Pin Data

		$verify = PinDataInfo::where("pin", $input)->first();

		//If PIN Not exist
		if(empty($verify))
		{
			$ussdtext = "Airvend 174\n";
			$ussdtext .= "You have input an Incorrect PIN\n";
			$ussdtext .= "Contact: info@callphoneng.com";
			$end = 1;
			$response = $this->responseConstruct($msisdn,$sessionid,$ussdtext,$end);
			return response($response)->header('Content-Type', 'application/xml');

		}

		if($verify->status == 0)
		{
			$ussdtext = "Airvend 174\n\n";
			$ussdtext .= "The PIN you entered has been used\n";
			$ussdtext .= "Contact: info@callphoneng.com";
			$end = 1;
			$response = $this->responseConstruct($msisdn,$sessionid,$ussdtext,$end);
			return response($response)->header('Content-Type', 'application/xml');
		}

		//Get AIRTIME Data

		if($mno == 'AIRTEL')$networkid = 1;
		if($mno == 'MTN')$networkid = 2;
		if($mno == 'GLO')$networkid = 3;
		if($mno == '9MOBILE')$networkid = 4;


		$amount = $verify->amount;

		//Check if Any Customer is Trying Also

		$update = PinDataInfo::where("pin", $verify->pin)
								->where("processing", '0')
								->update(["processing"=>'1']);

		if($update == false)
		{
			$ussdtext = "Airvend 174\n\n";
			$ussdtext .= "Initiated by another customer, try again\n";
			$end = 1;
			$response = $this->responseConstruct($msisdn,$sessionid,$ussdtext,$end);
			return response($response)->header('Content-Type', 'application/xml');
		}


		//Call API Associated with Input
		$ref_tag = "CPL-UNI-".date("Ymd")."-".uniqid();

		$st_len = strlen($msisdn);
		$num_st = $st_len - 10;
		$star =  substr($msisdn,$num_st);
		$msIsdn = "0".$star;
		$type = 1;
		$payload = array("ref"=>$ref_tag, "account"=>$msIsdn, "amount"=>$amount, "networkid"=>$networkid, "type"=>$type);

		$start_time = microtime(true);
		$call = new ApiController;
		$response = $call->airvendTransaction($payload);

		$end_time = microtime(true);

		$et = round($end_time - $start_time, 2);

		$output = json_decode($response, false);
		$response_code = $output->confirmationCode;

		if($output->confirmationCode == 200){
			$status_code = 0;
		}
		else{
			$status_code="999999";
		}


		$vend = new TransactionTable;

		if($status_code == "0")
		{	

			$nCal = new CalculatorController;
			$inc = $nCal->IncentiveCalc($amount, $verify->userid, $networkid);

			$vend->msisdn = $msisdn;
			$vend->pin =  $verify->pin;
			$vend->serial =  $verify->serial;
			$vend->incentive = $inc;
			$vend->amount = $verify->amount;
			$vend->userid = $verify->userid;
			$vend->sessionid = $sessionid;
			$vend->response =  $response;
			$vend->response_code = $response_code;
			$vend->networkid = $networkid;
			$vend->et = $et;
			$vend->status = $status_code;
			$vend->product_code = $type;
			$vend->ref_tag = $ref_tag;

			$vend->save();


			//Update Processing

			
			$datetime =  date("Y-m-d H:m:s");
			
			$update = PinDataInfo::where("pin", $verify->pin)
								->update(["processing"=>'2', "status"=>"0", "networkid"=>$networkid,"time_used"=>$datetime,"used_by"=>$msisdn, "remark"=>$status_code,"sessionid"=>$sessionid]);

			$ussdtext = "Airvend\n\n";
			$ussdtext .= "Successful Recharge of {$amount} to {$msisdn}\n";
			$ussdtext .= "Contact: info@callphoneng.com";
			$end = 1;
			$response = $this->responseConstruct($msisdn,$sessionid,$ussdtext,$end);
			return response($response)->header('Content-Type', 'application/xml');

		}
		else
		{

			$vend->msisdn = $msisdn;
			$vend->pin =  $verify->pin;
			$vend->serial =  $verify->serial;
			$vend->amount = $verify->amount;
			$vend->userid = $verify->userid;
			$vend->sessionid = $sessionid;
			$vend->response =  $response;
			$vend->response_code = $response_code;
			$vend->networkid = $networkid;
			$vend->status = $status_code;
			$vend->product_code = $type;
			$vend->ref_tag = $ref_tag;
			$vend->et = $et;

			$vend->save();


			//Update Processing

			$update = PinDataInfo::where("pin", $verify->pin)
								->update(["processing"=>'0']);

			$ussdtext = "Airvend\n\n";
			$ussdtext .= "Transaction Failed, Kindly try again Later\n";
			$ussdtext .= "Contact: info@callphoneng.com";
			$end = 1;
			$response = $this->responseConstruct($msisdn,$sessionid,$ussdtext,$end);
			return response($response)->header('Content-Type', 'application/xml');
		}





	}



}
