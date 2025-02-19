<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Venta extends Model
{
    use HasFactory;
    protected $connection ='mongodb';
    protected $collection = 'ventas';
    protected $fillable = [
        "guid",
        "lineasVenta"
    ];
}
