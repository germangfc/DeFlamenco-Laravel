<?php

namespace App\Models;

use App\utils\GuuidGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{

    use HasFactory;
    protected $table = "empresas";
    public $incrementing = false;
    protected $keyType ='string';

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
        return $this->belongsTo(User::class, 'usuario_id');
    }

    protected static function boot(){
        parent::boot();
        static::creating(function ($empresa){
            if (empty($empresa->id)) {
                $empresa->id = GuuidGenerator::generateHash();
            }
        });
    }


}
