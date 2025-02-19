<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LineaVenta extends Model
{
    use HasFactory;
    protected $connection ='mongodb';

    protected $fillable = [
        "guid",
        "idTicket",
        "precioVentaTicket",
        "isDeleted"
    ];
}
