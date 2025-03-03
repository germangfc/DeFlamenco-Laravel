<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Ticket;

class TicketController extends Controller
{
    public function index()
    {
        $idClient = auth()->user()->id;
        $tickets = Ticket::where('idClient', $idClient)->paginate(10);
        $eventos = Evento::all();

        return view('tickets.index', compact('tickets','eventos'));
    }

    public function validar($id)
    {
        // Buscar el ticket por su ID
        $ticket = Ticket::find($id);
        $evento = Evento::find($ticket->idEvent);

        if ($evento) {
            // Si el ticket existe, puedes mostrar la información de la entrada o hacer la validación
            return view('tickets.valido', ['ticket' => $evento]);
        } else {
            // Si no se encuentra el ticket, mostrar un mensaje de error
            return redirect()->route('eventos')->with('error', 'Ticket no válido.');
        }
    }
}
