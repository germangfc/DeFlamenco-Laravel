<?php

namespace App\Models;

use App\utils\GuuidGenerator;
use MongoDB\Laravel\Eloquent\Model as Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Ticket extends Model
{
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
