<?php 
namespace App\Http\Controllers\CalculatorControllers;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\CommissionTable;
use App\Models\IncentiveTable;


class CalculatorController extends Controller
{

	private function calculation($amount, $per){

		$comm = $amount * $per / 100;

		return $comm;
	}

	public function IncentiveCalc($amount, $userid, $networkid)
	{
		$per = IncentiveTable::where("userid", $userid)
								->where("networkid", $networkid)
								->first();


		$comm = $this->calculation($amount, $per->incentive);

		$wallet = Wallet::where("userid", $userid)->first();

		$wallet->incentive_balance = $wallet->incentive_balance + $comm;
		$wallet->push();
		return $comm;

		
	}

	public function CommissionCalc($amount, $userid)
	{
		$per = IncentiveTable::where("userid", $userid)
								->first();

		$comm = $this->calculation($amount, $per->commission);

		$wallet = Wallet::where("userid", $userid)->first();
		$wallet->commission_balance = $wallet->commission_balance + $comm;
		$wallet->push();
		return $comm;
	}
}