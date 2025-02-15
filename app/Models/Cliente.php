<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{

    protected $fillable = [
        'user_id',
        'nombre',
        'dni',
        'foto_dni',
        'lista_entradas',
        'is_deleted',
    ];

    protected $casts = [
        'lista_entradas' => 'array'
    ];

}
