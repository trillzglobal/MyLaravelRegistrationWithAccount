<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class TransactionPin extends Model
{
    use HasFactory;


    protected $fillable = [
    	"userid",
    	"pin_value",
    	"email",
    	"pin_amount",
    	"pin_count",
    	"ref_tag"
    ];

   protected $dateFormat = 'Y-m-d H:i:s';


    protected $hidden = [

    	"updated_at"
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
