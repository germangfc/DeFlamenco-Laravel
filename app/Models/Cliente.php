<?php

namespace App\Models;

use App\utils\GuuidGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     *
     * @property string $id  Identificador del cliente.
     *
     * @property string $user_id  Identificador del usuario.
     *
     * @property string $avatar  Avatar del cliente.
     *
     * @property array $lista_entradas  Lista de entradas del cliente.
     *
     * @property boolean $is_deleted  Indica si el cliente ha sido eliminado.
     *
     * @property User $user  Usuario asociado al cliente.
     *
     */


    use HasFactory;

    public $incrementing = false;
    protected $keyType ='string';
    protected $fillable = [
        'user_id',
        'avatar',
        'lista_entradas',
        'is_deleted',
    ];

    protected $casts = [
        'lista_entradas' => 'array'
    ];

    /**
     * Genera un identificador único para el cliente.
     *
     * @return void  Identificador único para el cliente.
     */

    protected static function boot()
    {
        parent::boot();
        static::creating(function($cliente){
            if (empty($cliente->id)){
                $cliente->id = GuuidGenerator::generateHash();
            }
        });
    }

    /**
     * Obtiene el cliente asociado a un usuario.
     *
     * @param $query Consulta a realizar.
     * @param $userid Identificador del usuario.
     * @return mixed Cliente asociado al usuario.
     */
    public function scopeFindByUserId($query, $userid)
    {
        return $query->where('user_id', $userid);
    }

    /**
     * Obtiene el usuario asociado al cliente.
     *
     * @return mixed Usuario asociado al cliente.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Busca un cliente por su email.
     *
     * @param $query Consulta a realizar.
     * @param $email Email del cliente.
     * @return mixed Cliente asociado al email.
     */
    public function scopeSearch($query, $term)
    {
        if (!$term) {
            return $query;
        }

        $term = strtolower($term);

        return $query->whereHas('user', function ($q) use ($term) {
            $q->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"])
                ->orWhereRaw('LOWER(email) LIKE ?', ["%{$term}%"]);
        });
    }


}
