<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $fillable = [
        "userid",
        "first_name",
        "last_name",
        "email",
        "phone_number",
        "password", 
        "gender",
        "dob",
        "user_bank", 
        "user_account",
        "address",
        "city",
        "country",
        "zip_code",
        "status",
        "flagged",
        "avatar"
    ];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
    Create Mutator
    */


    public function setUseridAttribute($userid){
        $userid = uniqid();
        $this->attributes['userid'] = strtoupper($userid);
    }
}
