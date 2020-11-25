<?php

namespace App\Http\Controllers\TransactionControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CommissionTable;
use App\Models\IncentiveTable;
use App\Models\PinNumber;
use App\Models\PinPrice;
use App\Models\TransactionTable;
use App\Models\TransactionPin;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserInformation extends Controller
{
    //Get Pin Number allowed and Amount Allowed

    public function pinAllowed(){

    	$pins = PinNumber::all();
    	$pin = [];
    	foreach($pins as $p){
    		$pin[] = $p['number'];
    	}
    	$values = PinPrice::all();
    	$value = [];

    	foreach($values as $v){
    		$value[] = $v['number'];
    	}


    	return response()->json(["number"=>$pin, "values"=>$value]);
    }


    public function pinGenerationHistory(Request $request)
    {   
        $userid = $request->user()->userid;
        $trans = TransactionPin::where("userid", $userid)->orderBy('created_at', 'DESC')->get();

        return response()->json(["transactions"=>$trans]);


    }

    public function transactionHistory(Request $request)
    {
    	//Get User Details
    	$userid = $request->user()->userid;

    	$transactions = TransactionTable::where("userid", $userid)->get();

    	return response()->json(["transactions"=>$transactions], 200);

    }


    public function incentive(Request $request)
    {
        $userid = $request->user()->userid;

        $incentive = IncentiveTable::where("userid", $userid)->get();
        $commission = CommissionTable::where("userid", $userid)->first();

        return response()->json(["incentive"=>$incentive, "commission"=>$commission]);

    }


}
