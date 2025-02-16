<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        "idEvent",
        "idClient",
        "price",
        "isReturned"
    ];
}
