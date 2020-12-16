<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncentiveTable extends Model
{
    use HasFactory;

    protected $fillable = [

    	"userid",
    	"incentive",
    	"networkid"
    ];


    protected $dateFormat = 'Y-m-d H:i:s';
}
