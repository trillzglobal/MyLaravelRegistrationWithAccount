<?php

namespace App\Http\Controllers\TransactionControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\TransactionPin;
use App\Models\PinBucket;
use App\Models\PinDataInfo;
use App\Models\CommissionTable as Commission;
use Illuminate\Support\Facades\Validator;


class TransactionPinController extends Controller
{
    //Controls the Pin Purchase of Customers


	public function purchasePin(Request $request)
	{
		//{Number of Pin, Amount}
		$rules = [
				"value"=>["required","integer", "min:1"],
				"amount"=>["required", "integer", "min:100"]
			];

		$validator = Validator::make($request->all(), $rules);

    	if($validator->fails()){
    		$errors = $validator->errors();
    		return response()->json($errors,302);
    	}

		$amount = $request->amount;
		$value = $request->value;
		$total = $value * $amount;

		$userid = $request->user()->userid;


		//Confirm if User have enough Money
		$balance = Wallet::where("userid", $userid)->first();
		$init_balance = $balance->wallet_balance;

		if($total > $init_balance)
		{
			return response()->json(["status"=>"error", "error"=>"Balance is too low for transaction", "balance"=>$init_balance], 200);
		}

		//Count Available Pins in PIN Bucket

		$count = PinBucket::where("status", "1")->count();
		
		if($count < $value)
		{
			return response()->json(["status"=>"error", "error"=>"Requested Number of PIN Can't Be Purchased Now", "available"=>$count], 200);
		}

		//Get PIN From PIN Bucket
		$pins = PinBucket::where("status", "1")
							->limit($value)
							->get();

		//Subtract Payment and Calculate Commission

		$comm = Commission::where("userid", $userid)->first();
		$uComm = $comm->commission_balance;

		$final_balance = $init_balance - $total;
		$pComm = $total * $uComm / 100 ;

		$final_commission = $balance->commission_balance + $pComm;

		//Update Wallet Balances

		$upUser = Wallet::where("userid", $userid)->first();

		$upUser->commission_balance =  $final_commission;
		$upUser->wallet_balance = $final_balance;

		$upUser->save();



		//Store New Pins in Pin Data Tables

		$ref_tag = "CPL"."_".date("Ymd")."_".uniqid();



		foreach($pins as $pin)
		{
			$save = new PinDataInfo;

			$save->userid = $userid;
			$save->email = $request->user()->email;
			$save->pin = $pin->pin;
			$save->serial = $pin->serial;
			$save->amount = $amount;
			$save->time_purchased = date("Y-m-d H:m:s");
			$save->status = 1;
			$save->processing = 0;
			$save->ref_tag = $ref_tag;

			$save->save();

			//Update PIN on PIN Bucket
			PinBucket::where('pin',$pin->pin)
					->update(
						["value"=>$amount,
						"bought_by"=>$userid,
						"status"=>0,
						"time_bought"=>date("Y-m-d H:m:s"),
						"ref_tag"=>$ref_tag
						]
					);
			
		}

		//Store PIN in Transaction PIN Table
		$transPin = new TransactionPin;

		$transPin->userid = $userid;
		$transPin->email = $request->user()->email;
		$transPin->pin_value = $amount;
		$transPin->pin_amount = $total;
		$transPin->pincount = $value;
		$transPin->ref_tag = $ref_tag;

		$transPin->save();
		
		return response()->json(["status"=>"success", "success"=>"Pin purchased Successfully", "pins"=>$pins],200);


	}


	public function pinSearch(Request $request, $ref_id)
	{
		$details = PinDataInfo::where("userid", $request->user()->userid)
					->where('ref_tag', $ref_id)
					->get();

		if(empty($details))
		{
			return response()->json(["status"=>"error", "message"=>"No Transaction for this Reference"], 200);

		}

		return response()->json(["status"=>"success", "details"=>$details]);
	}


	public function withdraw(Request $request)
	{
		$userid = $request->user()->userid;

		$rules = [
			"name" => ['required', 'string'],
			"amount"=> ['required']
		];

		$validator = Validator::make($request->all(),$rules);

		if($validator->fails())
		{
			$errors = $validator->errors();
    		return response()->json(["status"=>"error", "error"=>$errors],200);
		}

		$name =  $request->name;
		$amount = $request->amount;

		if($name == "incentive")
		{
			$inc = Wallet::where("userid", $userid)->first();

			if($inc->incentive_balance >= $amount )
			{
				$inc->wallet_balance = $inc->wallet_balance + $amount;
				$inc->incentive_balance = $inc->incentive_balance - $amount;

				$inc->save();
				return response()->json(["status"=>"success","message"=>"Successful withdrawal of Incentive"], 200);
			}
			else{
				return response()->json(["status"=>"error","error"=>"Balance low for requested Amount"], 200);
			}
		}
		elseif($name == "commission")
		{
			$inc = Wallet::where("userid", $userid)->first();

			if($inc->commission_balance >= $amount )
			{
				$inc->wallet_balance = $inc->wallet_balance + $amount;
				$inc->commission_balance = $inc->commission_balance - $amount;

				$inc->save();
				return response()->json(["status"=>"success","message"=>"Successful withdrawal of Incentive"], 200);
			}
			else{
				return response()->json(["status"=>"error","error"=>"Balance low for requested Amount"], 200);
			}
		}
		else{
			return response()->json(["status"=>"error","error"=>"Unknown Value to be Withdrawn"], 200);
		}
	}

}
