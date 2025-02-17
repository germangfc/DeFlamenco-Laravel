<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = "empresas";
    // Los campos requeridos
    protected $fillable = [
        'cif', 'nombre', 'direccion', 'cuentaBancaria', 'telefono', 'correo'
    ];
    // scope para el buscador de empresa
    public function scopeSearch($query, $name)
    {
        return $query->where('nombre', 'LIKE', "%$name%");
    }

}
