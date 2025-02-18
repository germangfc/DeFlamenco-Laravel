<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{

    use HasFactory;
    protected $fillable = [
        'user_id',
        'dni',
        'foto_dni',
        'lista_entradas',
        'is_deleted',
    ];

    protected $casts = [
        'lista_entradas' => 'array'
    ];

    public function scopeFindByDni($query, $dni)
    {
        return $query->where('dni', $dni);
    }

    public function scopeFindByUserId($query, $userid)
    {
        return $query->where('user_id', $userid);
    }

}
