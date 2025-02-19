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
        $ticketIds = Ticket::pluck('id')->toArray();

        return [
            'guid' => (string) Str::uuid(),  // Asegurar que el GUID se almacene correctamente como string
            'lineas_venta' => collect(range(1, rand(1, 5)))->map(function () use (&$ticketIds) {
                // Verificamos que queden tickets disponibles
                if (count($ticketIds) > 0) {
                    // Seleccionamos un ticket aleatorio y lo eliminamos de la lista
                    $randomKey = array_rand($ticketIds);
                    $idTicket = $ticketIds[$randomKey];
                    // Eliminamos el ticket de la lista para que no se repita
                    array_splice($ticketIds, $randomKey, 1);
                } else {
                    // Si no hay más tickets disponibles, asignamos null
                    $idTicket = null;
                }

                return [
                    'idTicket' => $idTicket,
                    'precio_unitario' => fake()->randomFloat(2, 1, 100),
                ];
            })->toArray(),
 /*           'guid' => (string) Str::uuid(),
            'lineas_venta' => collect(range(1, rand(1, 5)))->map(function () use ($ticketIds) {
                return [
                    //'idTicket' => !empty($ticketIds) ? $ticketIds[array_rand($ticketIds)] : null, // Ticket aleatorio
                    'idTicket' => !empty($ticketIds) ? array_pop($ticketIds) : null, // Tomar un ticket aleatorio y eliminarlo del array para evitar repeticiones
                    'precio_unitario' => fake()->randomFloat(2, 1, 100),
                ];
            })->toArray(),*/
        ];
    }
}
