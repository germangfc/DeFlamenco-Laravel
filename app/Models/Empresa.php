<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{

    use HasFactory;
    protected $table = "empresas";
    // Los campos requeridos
    protected $fillable = [
      'cif', 'name', 'direccion','imagen' ,'telefono', 'email','cuentaBancaria', 'usuario_id', 'lista_eventos','isDeleted'
    ];

    public function scopeSearch($query, $name)
    {
        return $query->where('name', 'LIKE', "%$name%");
    }

    protected $casts = [
        'lista_eventos'=>'array'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }


}
