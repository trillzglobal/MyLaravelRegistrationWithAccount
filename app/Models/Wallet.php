<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;


    protected $fillable = [
    	'userid',
    	'wallet_balance',
    	'referral_balance',
    	'bonus_balance'
    ];
}
