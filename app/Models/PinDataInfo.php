<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class PinDataInfo extends Model
{
    use HasFactory;


    protected $dateFormat = 'Y-m-d H:i:s';


    protected function serializeDate(DateTimeInterface $date)
	{
	    return $date->format('Y-m-d H:i:s');
	}
}
