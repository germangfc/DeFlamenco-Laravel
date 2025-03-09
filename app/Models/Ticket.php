<?php

namespace App\Models;

use App\utils\GuuidGenerator;
use MongoDB\Laravel\Eloquent\Model as Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Ticket extends Model
{
    /**
     * Modelo de Ticket.
     *
     * @var array
     *
     * @property string $id  Identificador del ticket.
     *
     * @property string $idEvent  Identificador del evento.
     *
     * @property string $idClient  Identificador del cliente.
     *
     * @property float $price  Precio del ticket.
     *
     * @property boolean $isReturned  Indica si el ticket ha sido devuelto.
     *
     */
    use HasFactory;

    protected $connection = 'mongodb';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        "idEvent",
        "idClient",
        "price",
        "isReturned"
    ];

    /**
     * Genera un identificador único para el ticket.
     *
     * @return void  Identificador único para el ticket.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($ticket) {
            if (empty($ticket->_id)) {
                $ticket->_id = GuuidGenerator::generateHash();
            }
        });
    }
}
