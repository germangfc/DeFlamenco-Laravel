<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;
    protected $connection ='mongodb';
    protected $fillable = [
        "idEvent",
        "idClient",
        "price",
        "isReturned"
    ];
}
