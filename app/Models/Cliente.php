<?php

namespace App\Models;

use App\utils\GuuidGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{

    use HasFactory;

    public $incrementing = false;
    protected $keyType ='string';
    protected $fillable = [
        'user_id',
        'dni',
        'foto_dni',
        'avatar',
        'lista_entradas',
        'is_deleted',
    ];

    protected $casts = [
        'lista_entradas' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function($cliente){
            if (empty($cliente->id)){
                $cliente->id = GuuidGenerator::generateHash();
            }
        });
    }

    public function scopeSearch($query, $dni)
    {
        return $query->where('dni', 'LIKE', "%$dni%");
    }

    public function scopeFindByDni($query, $dni)
    {
        return $query->where('dni', $dni);
    }

    public function scopeFindByUserId($query, $userid)
    {
        return $query->where('user_id', $userid);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
