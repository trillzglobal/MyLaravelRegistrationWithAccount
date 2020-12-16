<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PinBucket extends Model
{
    use HasFactory;

    protected $fillable =[
    	"pin", "value", "bought_by", "status", "time_bought", "ref_tag"
    ];

    protected $dateFormat = 'Y-m-d H:i:s';


    protected function serializeDate(DateTimeInterface $date)
	{
	    return $date->format('Y-m-d H:i:s');
	}
}
