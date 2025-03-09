<?php

namespace App\Models;

use App\utils\GuuidGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    /**
     * Modelo Empresa que representa a una empresa.
     *
     * @var array
     *
     * @property string $id  Identificador de la empresa.
     *
     * @property string $cif  CIF de la empresa.
     *
     * @property string $name  Nombre de la empresa.
     *
     * @property string $direccion  Dirección de la empresa.
     *
     * @property string $imagen  Imagen de la empresa.
     *
     * @property string $telefono  Teléfono de la empresa.
     *
     * @property string $email  Email de la empresa.
     *
     * @property string $cuentaBancaria  Cuenta bancaria de la empresa.
     *
     * @property string $usuario_id  Identificador del usuario.
     *
     * @property boolean $isDeleted  Indica si la empresa ha sido eliminada.
     *
     * @property User $usuario  Usuario asociado a la empresa.
     *
     * @property Evento[] $eventos  Eventos asociados a la empresa.
     *
     */

    use HasFactory;
    protected $table = "empresas";
    public $incrementing = false;
    protected $keyType ='string';

    protected $fillable = [
      'cif', 'name', 'direccion','imagen' ,'telefono', 'email','cuentaBancaria', 'usuario_id','isDeleted'
    ];

    /**
     * @param $query
     * @param $term
     * @return mixed
     */
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

    /**
     * Obtiene el usuario asociado a la empresa.
     *
     * @return mixed Usuario asociado a la empresa.
     */

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Genera un identificador único para la empresa.
     *
     * @return void  Identificador único para la empresa.
     */
    protected static function boot(){
        parent::boot();
        static::creating(function ($empresa){
            if (empty($empresa->id)) {
                $empresa->id = GuuidGenerator::generateHash();
            }
        });
    }

    /**
     *Relación uno a muchos con Evento.
     *
     * @return mixed Eventos asociados a la empresa.
     */

    public function eventos()
    {
        return $this->hasMany(Evento::class, 'empresa_id', 'id');
    }

}
