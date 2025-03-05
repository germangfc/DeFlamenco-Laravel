<?php
namespace App\Services;

use App\Models\Cliente;
use App\Models\Ticket;
use App\Models\Venta;
use App\Models\Evento;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VentaService
{
    public function generarVenta($ticketList, User $user){
        $guidVenta = (string) Str::uuid();
        $lineasVenta = [];
        $evento = new Evento();

        // Para cada ticket de la lista creamos una línea de venta y la añadimos a la lista
        foreach ($ticketList as $ticket) {
            $event = Evento::find ($ticket->idEvent);
            if (!$event) {
                Log::error('Error al generar venta: El evento no existe');
                return null;
            }
            $evento = $event;
            /*$lineaVenta=[];
            array_push($lineaVenta, $ticket->_id, $ticket->precio, $event->nombre, $event->fecha, $event->hora, $event->ciudad);
            $lineasVenta[]= $lineaVenta;*/
            $lineasVenta[]=[
                $ticket->_id, $ticket->price, $event->nombre, $event->fecha, $event->hora, $event->ciudad
            ]; // se añade una línea de venta a la lista de lineas de venta
        }

        $venta = new Venta();
        $venta->guid = $guidVenta;
        $venta->lineasVenta = $lineasVenta;
        $venta->save();

        $evento->stock--;
        return $venta;

    }
    public function generarTickets($cart, $user){
        $ticketList=[];

       /* if (!$cart) {
            Log::error('Error al generar tickets: El carrito está vacío');
            return null;
        }*/

        //Generar tickets, uno por cada línea del carrito, y tantas como indique el campo cantidad de esa línea del carrito
        foreach ($cart as $item) {

            for($i=0; $i < $item['quantity']; $i++) {

                $ticket = $this->generarTicket($item['idEvent'], $user);
                if (!$ticket) {
                    Log::error('Error al generar ticket de evento: No se pudo generar el ticket');
                    return null;
                }
                $ticketList[] = $ticket;
            }

        }

        return $ticketList;

    }
    public function generarTicket($idEvent,User $user){

        //Validar que el evento exista
        $event = Evento::find($idEvent);
        if (!$event) {
            Log::error('Error al generar ticket de evento: El evento no existe');
            return null;
        }

        //Obtener el cliente
        $cliente = Cliente::FindByUserId($user->id)->first();
        if (!$cliente) {
            Log::info('Error al generar ticket de evento: El cliente no existe');
            return null;
        } else { Log::info('Cliente encontrado');}// Log::info('Tipo de cliente: ' . get_class($cliente));}

        //Generar ticket
        $ticket = new Ticket;
        $ticket->idEvent = $idEvent;
        $ticket->idClient = $cliente->id;
        $ticket->price = $event->precio;
        $ticket->isReturned = false;
        $ticket->save();

        return $ticket;

    }
}

