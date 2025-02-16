<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $connection = 'mongodb';

    protected $fillable = [
        "idEvent",
        "idClient",
        "price",
        "isReturned"
    ];

}
