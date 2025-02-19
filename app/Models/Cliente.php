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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
