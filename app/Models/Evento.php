<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $table = 'eventos';

    protected $fillable = [
        'nombre',
        'stock',
        'fecha',
        'hora',
        'direccion',
        'ciudad',
        'precio',
    ];
    protected $primarykey = 'id';

    public function scopeSearch($query, $name)
    {
        return $query->where('nombre', 'LIKE', "%$name%");
    }
}
