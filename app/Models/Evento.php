<?php

namespace App\Models;

use App\utils\GuuidGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'eventos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'stock',
        'fecha',
        'hora',
        'direccion',
        'ciudad',
        'precio',
        'foto',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function($evento){
            if (empty($evento->id)){
                $evento->id = GuuidGenerator::generateHash();
            }
        });
    }

    protected $primaryKey = 'id';

    public function scopeSearch($query, array $filters)
    {
        return $query
            ->when($filters['query'] ?? null, function ($q, $term) {
                $term = strtolower($term);
                $q->where(function ($q2) use ($term) {
                    $q2->whereRaw('LOWER(nombre) LIKE ?', ["%{$term}%"])
                        ->orWhereRaw('LOWER(ciudad) LIKE ?', ["%{$term}%"])
                        ->orWhereRaw('LOWER(direccion) LIKE ?', ["%{$term}%"]);
                });
            })
            ->when($filters['fecha'] ?? null, function ($q, $fecha) {
                $q->whereDate('fecha', '=', $fecha);
            })
            ->when($filters['precio_min'] ?? null, function ($q, $precioMin) {
                $q->where('precio', '>=', $precioMin);
            })
            ->when($filters['precio_max'] ?? null, function ($q, $precioMax) {
                $q->where('precio', '<=', $precioMax);
            });
    }




    public function scopeFindById($query, $id)
    {
        return $query->where('id', $id);
    }
}
