<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionTable extends Model
{
    use HasFactory;

    protected $fillable = [
    	"userid", "networkid", "amount", "incentive", "commission", "referral_bonus", "msisdn", 
    	"response", "et", "sessionid", "pin", "status", "serial", "product_code", "response_code",
    	"ref_tag"


    ];

    protected $hidden =[
    	"updated_at",
    	"response"
    ];
}
