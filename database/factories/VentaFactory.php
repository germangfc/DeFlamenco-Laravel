<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Ticket;
use Illuminate\Support\Str;
use App\Models\Venta;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Venta>
 */
class VentaFactory extends Factory
{
    protected $model = Venta::class;


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Obtener todos los IDs de la colección Ticket
        $ticketIds = Ticket::pluck('_id')->toArray();

        return [
            'guid' => Str::uuid(),
            'lineas_venta' => collect(range(1, rand(1, 5)))->map(function () use ($ticketIds) {
                return [
                    'idTicket' => !empty($ticketIds) ? $ticketIds[array_rand($ticketIds)] : null, // Ticket aleatorio
                    'precio_unitario' => fake()->randomFloat(2, 1, 100),
                ];
            })->toArray(),
        ];
    }
}
