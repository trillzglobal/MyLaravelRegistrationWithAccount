<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestStore extends Model
{
    use HasFactory;


    protected $fillable = [
    	"request", "ip", "device", "endpoint", "response"
    ];
}
