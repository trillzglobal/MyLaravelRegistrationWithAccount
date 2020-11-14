<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UpdateProfileController extends Controller
{
    //

    public function __construct(){

    	$this->middleware('auth');
    }



    protected function validator(array $data)
    {
        return Validator::make($data, [
            'address'=>['required', 'string', 'max:255'],
            'city'=>['required', 'string', 'max:50'],
            'country'=>['required', 'string', 'max:15'],
            'zip_code' => ['required', 'string', 'max:6']
        ]);
    }


    protected function update(Request $request){

        


    }
}
