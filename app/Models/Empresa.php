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
      'cif', 'name', 'direccion','imagen' ,'telefono', 'email','cuentaBancaria', 'usuario_id','isDeleted'
    ];

    public function scopeSearch($query, $term)
    {
        if (!$term) {
            return $query;
        }

        $term = strtolower($term);

        return $query->where(function ($q) use ($term) {
            $q->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"])
                ->orWhereRaw('LOWER(cif) LIKE ?', ["%{$term}%"])
                ->orWhereRaw('LOWER(email) LIKE ?', ["%{$term}%"]);
        });
    }

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


    public function eventos()
    {
        return $this->hasMany(Evento::class, 'empresa_id', 'id');
    }

}
