<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionTable extends Model
{
    use HasFactory;

    protected $fillable = [

    	"userid",
    	"commission"
    ];


    protected $dateFormat = 'Y-m-d H:i:s';
}
