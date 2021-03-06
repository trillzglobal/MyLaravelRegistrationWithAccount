<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PinPrice extends Model
{
    use HasFactory;

     protected $hidden = [
    	"created_at", "updated_at"
    ];


    protected $dateFormat = 'Y-m-d H:i:s';

}
