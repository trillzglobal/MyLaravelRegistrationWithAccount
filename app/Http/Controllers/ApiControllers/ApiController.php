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

	   	$create = array("name"=> $input["name"],
	                        "email"=> $input["email"],
	                        "phoneNumber"=> $input['phone_number'],
	                        "sendNotifications"=> false);
	        $detail = array("customer"=>$create);
	        $response = (new PayantCallController)->reserveAccountPayant($detail);
	        $output = json_decode($response, true);

	        if($output['statusCode'] == 200){

		        $resp['account_number'] = $output['data']['accountNumber'];
		        $resp['bank_name'] = "STERLING BANK";

		    }

		    return $resp;
   }
}
