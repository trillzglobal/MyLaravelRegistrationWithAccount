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
    	'commission_balance',
    	'incentive_balance'
    ];

    protected $dateFormat = 'U';
}
