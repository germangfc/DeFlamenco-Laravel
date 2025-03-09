<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Venta extends Model
{

    /**
     * Modelo Venta que representa a la venta de las entradas de un evento.
     *
     * @var array
     *
     * @property string $guid  Identificador de la venta.
     *
     * @property array $lineasVenta  Lineas de venta de la venta.
     *
     */
    use HasFactory;
    protected $connection ='mongodb';
    protected $collection = 'ventas';
    protected $fillable = [
        "guid",
        "lineasVenta"
    ];
}
