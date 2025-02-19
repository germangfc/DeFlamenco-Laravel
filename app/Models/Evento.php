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
        'foto',
    ];
    protected $primarykey = 'id';

    public function scopeSearch($query, $name)
    {
        return $query->where('nombre', 'LIKE', "%$name%");
    }

    public function scopeFindById($query, $id)
    {
        return $query->where('id', $id);
    }
}
