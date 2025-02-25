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
                'lineasVenta' => [],
            ];
        }

        // Determinar cuántas líneas tendrá esta venta (máximo 5, pero sin pasarnos de los tickets disponibles)
        $numLineasVenta = min(rand(1, 5), count(self::$availableTicketsIds));

        // Extraer los tickets para esta venta
        $lineasVenta = [];
        for ($i = 0; $i < $numLineasVenta; $i++) {
            $lineasVenta[] = [
                'idTicket' => array_shift(self::$availableTicketsIds), // Elimina el ticket del array
                'precioUnitario' => fake()->randomFloat(2, 1, 100),
            ];
        }

        return [
            'guid' => (string) Str::uuid(),
            'lineasVenta' => $lineasVenta, // Se asignan las líneas de venta
        ];
    }

}
