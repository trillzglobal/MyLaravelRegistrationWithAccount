<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ApiControllers\ApiController;
use App\Models\BaseCommission;
use App\Models\BaseIncentive;
use App\Models\CommissionTable;
use App\Models\IncentiveTable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ApiAuthController extends Controller
{
    //

    public function signup(Request $request)
    {
    	
    	$rules = [
            'first_name'=>['required', 'string', 'max:255'],
            'last_name'=>['required', 'string', 'max:255'],
            'phone_number'=>['required', 'max:15', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'dob' => ['date_format:Y-m-d','before:12 years'],
            'gender' => ['required']
        ];

        $message = [
        	"first_name"=> "First Name Must be String below 255 Characters",
        	"last_name"=> "Last Name Must be String below 255 Characters",
        	"email.unique" => "Email is already registered.",
        	"email.min" => "Password must be 8 digit and above",
        	"dob.before"=> "You must be above 12 years to register"

        ];
    	
    	$validator = Validator::make($request->all(), $rules, $message);

        


    	if($validator->fails()){
    		$errors = $validator->errors();
    		return response()->json(["status"=>"error", "error"=>$errors],200);
    	}

    		
        $vas = new ApiController;
        $payload = ['name'=> $request->input('last_name').' '.$request->input('first_name'),
                    'email'=>$request->input('email'),
                    'phone_number'=>$request->input('phone_number')
                ];
       
        $output = $vas->createAccount($payload);
        if($output == false)return response()->json(["status"=>"error", "error"=>"Can't Create account at the moment"],200);
        $user = new User([
            'userid' => uniqid(),
            'email' => $request->input('email'),
            'first_name'=> $request->input('first_name'),
            'last_name'=>$request->input('last_name'),
            'phone_number'=>$request->input('phone_number'),
            'gender'=>$request->input('gender'),
            'dob'=>date('Y-m-d',strtotime($request->input('dob'))),
            'user_bank' => $output['bank_name'],
            'user_account' => $output['account_number'],
            'status'=>1,
            'flagged'=>0,
            'password' => Hash::make($request->input('password')),
            'api_token' => Str::random(60)

        ]);


        $user->save();


        $wallet = new Wallet([
        	'userid' => $user->userid,
        	'wallet_balance'=>'0',
        	'commission_balance'=>'0',
        	'incentive_balance'=>'0'
        ]);

        $wallet->save();


        $incentive = BaseIncentive::all();
        $commission = BaseCommission::first();


        $usercomm = new CommissionTable;
        $usercomm->userid = $user->userid;
        $usercomm->commission = $commission->commission;
        $usercomm->save();

        foreach($incentive as $inc)
        {
            $userinc = new IncentiveTable;

            $userinc->userid = $user->userid;
            $userinc->networkid = $inc->networkid;
            $userinc->incentive = $inc->incentive;

            $userinc->save();
        }

        return response()->json(["status"=>"success", "message"=>"user registered successfully. Kindly Login to your mail to activate ".$request->email], 200);


    }

    public function profile (Request $request){

        $user = $request->user();
         $wallet = Wallet::where('userid', $user->userid)->first();
       
        $user->wallet = $wallet->wallet_balance;
        $user->incentive = $wallet->incentive_balance;
        $user->commission = $wallet->commission_balance;
 
        return response()->json(["status"=>"success",
            "user"=>$user
        ], 200);
    }

    public function tokenStatus(Request $request){

        return response()->json(["status"=>"success", "userid"=> $request->user()->userid], 200);
    }

    public function login (Request $request)
    {
    	$rules = [
            'email' => 'required|string',
            'password' => 'required|string'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
        	$errors = $validator->errors();
        	return response()->json(["status"=>"error", "error"=>$errors],200);
        }
 
        $credentials = request(['email', 'password']);
 
        if(!Auth::attempt($credentials)){
            return response()->json([
                'status'=>'error','message'=> 'Invalid email or password'
            ], 200);
        }
 
        $user = $request->user();
 
        $token = $user->createToken('Access Token');

        $wallet = Wallet::where('userid', $user->userid)->first();

        

       
        $user->wallet = $wallet->wallet_balance;
        $user->incentive = $wallet->incentive_balance;
        $user->commission = $wallet->commission_balance;

        $user->access_token = $token->accessToken;
 
        return response()->json(["status"=>"success",
            "user"=>$user
        ], 200);

    }

    public function logout(Request $request)
    {
    	$response = $request->user()->token()->revoke(); 

    		return response()->json([
	            "message"=>"User logged out successfully"
	        ], 200);
    
        

    }

    public function index()
    {
    	return response()->json(["message"=>"Welcome, Our Documentation does not cover the home"], 200);
    }

    public function forgot(Request $request)
    {

    	$rules = ["email"=>["email","required","string", "unique:users"]];
    	$validator = Validator::make($request->all(), $rules);

    	if($validator->fails()){
        	$error = $validator->errors();
        	return response()->json($error, 401);
        }

        Password::sendResetLink($request->email);

        return response()->json(["message" => 'Reset password link sent on your email.'], 200);


    }

    public function reset(Request $request)
    {
    	$rules = ['email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed'
        ];
    	$credentials = request()->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed'
        ]);

        $reset_password_status = Password::reset($credentials, function ($user, $password) {
            $user->password = $password;
            $user->save();
        });

        if ($reset_password_status == Password::INVALID_TOKEN) {
            return response()->json(["msg" => "Invalid token provided"], 400);
        }

        return response()->json(["msg" => "Password has been successfully changed"], 200);
    }
}
