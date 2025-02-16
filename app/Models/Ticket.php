<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as Model;

class Ticket extends Model
{
    protected $connection ='mongodb';
    protected $fillable = [
        "idEvent",
        "idClient",
        "price",
        "isReturned"
    ];
}
