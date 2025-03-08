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

        $tickets = Ticket::where('idClient', $idClient)->get();

        $eventIds = $tickets->pluck('idEvent')->toArray();

        $eventos = Evento::whereIn('id', $eventIds)
            ->orderBy('fecha', 'desc') // Ordena por fecha descendente
            ->get()
            ->keyBy('id');

        $tickets = $tickets->map(function ($ticket) use ($eventos) {
            $ticket->evento = $eventos[$ticket->idEvent] ?? null;
            return $ticket;
        });

        $tickets = $tickets->sortByDesc(fn($ticket) => $ticket->evento->fecha ?? '1970-01-01');

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
