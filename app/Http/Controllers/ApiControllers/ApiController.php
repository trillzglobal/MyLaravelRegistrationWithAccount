<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class ApiController extends Controller
{
    //

    /*

   Controls API Connection
   */


   /*
   Sterling Account Creation
   */

   public function  createAccount(array $input){

	   	$create = array("accountName"=> $input["name"],
	                        "customerEmail"=> $input["email"],
	                        "phoneNumber"=> $input['phone_number'],
	                        "accountReference"=> $input['phone_number']);
	        $detail = $create;
	        $response = (new PayantCallController)->reserveAccountPayant($detail);
	        $output = json_decode($response, true);

	        if($output['statusCode'] == 200){

		        $resp['account_number'] = $output['accountNumber'];
		        $resp['bank_name'] = "STERLING BANK";

		    }
        else{
          $resp  = false;
        }

		    return $resp;
   }

   public function airvendTransaction(array $input)
   {
   		$url = config('app.AIR_URL');
		$username = config('app.AIR_USERNAME');
		$password = config('app.AIR_PASSWORD');
		$hash_key =config('app.AIR_KEY');
		$content = array("details"=>$input);
		$content = json_encode($content);
		$hash = $content.$hash_key;
		$hash = hash("sha512", $hash);
		//$content = json_encode($content);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json","username:$username","password:$password","hash:$hash"));     
        	curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$content);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
   }


   
}
