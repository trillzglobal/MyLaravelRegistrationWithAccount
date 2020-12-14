<?php

namespace App\Http\Controllers\TransactionControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CommissionTable;
use App\Models\IncentiveTable;
use App\Models\PinNumber;
use App\Models\PinDataInfo;
use App\Models\PinPrice;
use App\Models\TransactionTable;
use App\Models\TransactionPin;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserInformationController extends Controller
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
    		$value[] = $v['price'];
    	}


    	return response()->json(["number"=>$pin, "values"=>$value]);
    }


    public function pinGenerationHistory(Request $request)
    {   
        $userid = $request->user()->userid;
        $trans = TransactionPin::where("userid", $userid)->orderBy('created_at', 'DESC')->get();

         foreach($trans as $transaction)
        {
            $ref = $transaction->ref_tag;
            $used = PinDataInfo::where("userid", $userid)
                                        ->where("ref_tag",$ref)
                                        ->where("status", 0)
                                        ->count();
            $transaction->used = $used;
        }


        return response()->json(["transactions"=>$trans]);


    }

    public function transactionHistory(Request $request)
    {
    	//Get User Details
    	$userid = $request->user()->userid;

    	$transactions = TransactionTable::where("userid", $userid)->
                                            where("status", "0")->get();


    	return response()->json(["transactions"=>$transactions], 200);

    }


    public function incentive(Request $request)
    {
        $userid = $request->user()->userid;

        $incentive = IncentiveTable::where("userid", $userid)->get();
        $commission = CommissionTable::where("userid", $userid)->first();

        return response()->json(["incentive"=>$incentive, "commission"=>$commission]);

    }

    public function transactionSummary(Request $request)
    {
        $userid = $request->user()->userid;

        $mno = ["airtel"=>1,"mtn"=>2, "glo"=>3,"etisalat"=>4];

        $sum_sales = $sum_incentive = [];
        foreach($mno as $key=>$m)
        {
            $sum_mno = TransactionTable::where('userid', $userid)
                                        ->where('status',0)
                                        ->where('networkid', $m)
                                        ->where('created_at', '>=', date('Y-m-'))
                                        ->sum('amount');

            $sum_sales[$key] = $sum_mno;
            $sum_inc = TransactionTable::where('userid', $userid)
                                        ->where('status',0)
                                        ->where('networkid', $m)
                                        ->where('created_at', '>=', date('Y-m-'))
                                        ->sum('incentive');

            $sum_incentive[$key] = $sum_inc;
        }

        $pDay = date('Y-m-d');

        $fDay = date('Y-m-01');
        

        $currentDate = $fDay;

        $daily = [];
        $d = 1;
        $i = 1;
        
        while($currentDate <= $pDay)
        {   
            $daily_sum = TransactionTable::where('userid', $userid)
                                        ->where('status',0)
                                        ->where('created_at', '>=', $currentDate)
                                        ->where('created_at', '<', date("Y-m-d", strtotime($currentDate." +1 day")))
                                        ->sum('amount');
            
            $currentDate = date("Y-m-d", strtotime($currentDate." +1 day"));

            $daily["day ".$i]= $daily_sum;
            $i++;

        }

        return response()->json(["mno_sales"=>$sum_sales, "mno_incentive"=>$sum_incentive, "daily"=>$daily], 200);

    }


}
