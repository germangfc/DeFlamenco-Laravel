<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Evento;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition()
    {
        $evento = Evento::inRandomOrder()->first();
        $cliente = Cliente::inRandomOrder()->first();

        return [
            'idEvent'   => $evento ? $evento->id : null,
            'idClient'  => $cliente ? $cliente->id : null,
            'price'     => $evento ? $evento->precio : $this->faker->randomFloat(2, 10, 1000),
            'isReturned'=> false,
        ];
    }
}
