<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiControllers\ApiController;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {

        return Validator::make($data, [
            'first_name'=>['required', 'string', 'max:255'],
            'last_name'=>['required', 'string', 'max:255'],
            'phone_number'=>['required', 'string', 'max:15'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'dob' => ['date_format:Y-m-d','before:8 years'],
            'gender' => ['required']
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */

    protected function create(array $data)
    {   
        $vas = new ApiController;
        $payload = ['name'=> $data['last_name'].' '.$data['first_name'],
                    'email'=>$data['email'],
                    'phone_number'=>$data['phone_number']
                ];

        $output = $vas->createAccount($payload);
        return User::create([
            'userid' => uniqid(),
            'email' => $data['email'],
            'first_name'=> $data['first_name'],
            'last_name'=>$data['last_name'],
            'phone_number'=>$data['phone_number'],
            'gender'=>$data['gender'],
            'dob'=>date('Y-m-d',strtotime($data['dob'])),
            'user_bank' => $output['bank_name'],
            'user_account' => $output['account_number'],
            'status'=>1,
            'flagged'=>0,
            'password' => Hash::make($data['password']),
            'api_token' => Str::random(60)

        ]);
    }
}
