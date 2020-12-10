<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestTable extends Model
{
    use HasFactory;


    protected $fillable = [
    	"msisdn", "sessionid", "mno", "ussd_data"
    ];
}
