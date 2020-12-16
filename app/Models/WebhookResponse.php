<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookResponse extends Model
{
    use HasFactory;


    protected $fillable = [
    		"amount",
    		"userid",
    		"email",
    		"response",
    		"method",
    		"paidon",
    		"status",
    		"exchanger_reference"

    		];


    protected $dateFormat = 'U';


}
