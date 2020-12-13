<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PinBucket extends Model
{
    use HasFactory;

    private $fillable =[
    	"pin", "value", "bought_by", "status", "time_bought", "ref_tag"
    ];
}
