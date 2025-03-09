<?php

namespace App\Models;

use App\utils\GuuidGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    /**
     * Modelo Evento que representa a un evento.
     *
     * @var array
     *
     * @property string $id  Identificador del evento.
     *
     * @property string $nombre  Nombre del evento.
     *
     * @property string $descripcion  Descripción del evento.
     *
     * @property int $stock  Stock del evento.
     *
     * @property string $fecha  Fecha del evento.
     *
     * @property string $hora  Hora del evento.
     *
     * @property string $direccion  Dirección del evento.
     *
     * @property string $ciudad  Ciudad del evento.
     *
     * @property float $precio  Precio del evento.
     *
     * @property string $foto  Foto del evento.
     *
     * @property string $empresa_id  Identificador de la empresa.
     *
     * @property Empresa $empresa  Empresa asociada al evento.
     *
     */
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
        'empresa_id'
    ];

    /**
     * Genera un identificador único para el evento.
     *
     * @return void  Identificador único para el evento.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function($evento){
            if (empty($evento->id)){
                $evento->id = GuuidGenerator::generateHash();
            }
        });
    }

    /**
     * Relacion muchops a uno con una empresa.
     *
     * @return mixed Empresa asociada a eventos.
     */

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id', 'id');
    }

    protected $primaryKey = 'id';

    /**
     * Busca eventos por diferentes criterios.
     *
     * @param Builder $query  Consulta a realizar.
     * @param array $filters  Filtros a aplicar.
     *
     * @return Builder Consulta con los filtros aplicados.
     */
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

    /**
     * Busca un evento por su nombre.
     *
     * @param Builder $query Consulta a realizar.
     * @param $id
     * @return Builder Consulta con el nombre del evento.
     */
    public function scopeFindById($query, $id)
    {
        return $query->where('id', $id);
    }

    /**
     * Devuelve los eventos más recientes.
     *
     * @param Builder $query Consulta a realizar.
     * @param int $limit  Limite de eventos a devolver.
     *
     * @return Builder Consulta con los eventos más recientes.
     */
    public function scopeRecientes(Builder $query, int $limit = 10): Builder
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }
}
