<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiControllers\ApiController;
use App\Models\User;

class UpdateAccount extends Controller
{

	public function updateAccount(){

		//Select Users all;

		$users =  User::where("id", ">", 10)->get();
		foreach($users as $user){
			$vas = new ApiController;
        	$payload = ['name'=> $user->last_name.' '.$user->first_name,
                    'email'=>$user->email,
                    'phone_number'=>$user->phone_number
                ];
            $output = $vas->createAccount($payload);
            
        	if($output == false)return response()->json(["status"=>"error", "error"=>"Can't Create account at the moment"],200);

            User::where("id",$user->id)
            		->update(["user_account"=>$output["account_number"]]);
		}
	}

}
