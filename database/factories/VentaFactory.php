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
    //protected $model = Venta::class;
    protected static $availableTicketsIds = null;

    public function definition()
    {
        // Si es la primera vez que se ejecuta, inicializamos los tickets
        if (self::$availableTicketsIds === null) {
            self::$availableTicketsIds = Ticket::pluck('id')->toArray();
            shuffle(self::$availableTicketsIds);
        }

        // Si ya no quedan tickets, devolvemos una venta vacía (evita errores si se crean más ventas de las necesarias)
        if (empty(self::$availableTicketsIds)) {
            return [
                'guid' => (string) Str::uuid(),
                'lineas_venta' => [],
            ];
        }

        // Determinar cuántas líneas tendrá esta venta (máximo 5, pero sin pasarnos de los tickets disponibles)
        $numLineasVenta = min(rand(1, 5), count(self::$availableTicketsIds));

        // Extraer los tickets para esta venta
        $lineasVenta = [];
        for ($i = 0; $i < $numLineasVenta; $i++) {
            $lineasVenta[] = [
                'idTicket' => array_shift(self::$availableTicketsIds), // Elimina el ticket del array
                'precio_unitario' => fake()->randomFloat(2, 1, 100),
            ];
        }

        return [
            'guid' => (string) Str::uuid(),
            'lineas_venta' => $lineasVenta, // Se asignan las líneas de venta
        ];
    }

}

/*    public function definition(): array
    {
        static $availableTicketIds = null;

        // Si es la primera vez, obtenemos los IDs de la colección Ticket
        if ($availableTicketIds === null) {
            $availableTicketIds = Ticket::pluck('id')->toArray();
            shuffle($availableTicketIds); // Barajamos los tickets para aleatoriedad
        }

        // Determinar cuántas líneas de venta tendrá esta venta
        $numLineas = rand(1, min(5, count($availableTicketIds))); // Evitar pedir más de los disponibles

        // Extraer los IDs únicos para esta venta
        $selectedTickets = array_splice($availableTicketIds, 0, $numLineas);

        return [
            'guid' => (string) Str::uuid(),
            'lineas_venta' => collect($selectedTickets)->map(function ($ticketId) {
                return [
                    'idTicket' => $ticketId,
                    'precio_unitario' => fake()->randomFloat(2, 1, 100),
                ];
            })->toArray(),
        ];
    }*/
