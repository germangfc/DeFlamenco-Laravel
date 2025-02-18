<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    /**
     * @var int|mixed|string|null
     */
    public mixed $usuario_id;
    protected $table = "empresas";
    // Los campos requeridos
    protected $fillable = [
       'usuario_id', 'cif', 'nombre', 'direccion','imagen' ,'telefono', 'email','cuentaBancaria', 'usuario_id', 'lista_eventos','isDeleted'
    ];

    public function scopeSearch($query, $name)
    {
        return $query->where('nombre', 'LIKE', "%$name%");
    }

    protected $casts = [
        'lista_eventos'=>'array'
    ];


}
