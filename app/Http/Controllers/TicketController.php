<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        $idClient = auth()->user()->cliente()->first()->id;
        // Buscar los tickets en MongoDB
        $tickets = Ticket::where('idClient',  $idClient)->get();

        // Obtener los IDs de eventos únicos
        $eventIds = $tickets->pluck('idEvent')->toArray();

        // Buscar los eventos en PostgreSQL
        $eventos = Evento::whereIn('id', $eventIds)->get()->keyBy('id');

        // Agregar los eventos a los tickets
        $tickets = $tickets->map(function ($ticket) use ($eventos) {
            $ticket->evento = $eventos[$ticket->idEvent] ?? null;
            return $ticket;
        });

        return view('tickets.index', compact('tickets'));
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
