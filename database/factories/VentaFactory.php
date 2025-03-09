<?php

namespace Database\Factories;

use App\Models\Evento;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Ticket;
use Illuminate\Support\Str;
use App\Models\Venta;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Venta>
 */
class VentaFactory extends Factory
{
    protected static $availableTicketsIds = null;

    public function definition()
    {
        // Si es la primera vez que se ejecuta, inicializamos los tickets
        if (self::$availableTicketsIds === null) {
            // Aseguramos que hay tickets válidos con eventos asociados
            self::$availableTicketsIds = Ticket::whereNotNull('idEvent')->pluck('id')->toArray();
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
            // Buscar ticket por ID
            $ticket = Ticket::find(array_shift(self::$availableTicketsIds));

            // Verificar si el ticket existe
            if (!$ticket) {
                continue; // Si el ticket no existe, pasamos al siguiente
            }

            // Buscar evento asociado al ticket
            $event = Evento::find($ticket->idEvent);

            // Verificar si el evento existe
            if (!$event) {
                continue; // Si el evento no existe, pasamos al siguiente
            }

            // Agregar la línea de venta con datos del ticket y evento
            $lineasVenta[] = [
                 $ticket->_id,
                 $ticket->price,
                 $event->nombre,
                 $event->fecha,
                 $event->hora,
                 $event->ciudad,
            ];
        }

        return [
            'guid' => (string) Str::uuid(),
            'lineasVenta' => $lineasVenta, // Se asignan las líneas de venta
        ];
    }
}
