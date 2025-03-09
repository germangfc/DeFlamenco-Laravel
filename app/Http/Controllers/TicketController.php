<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{

    /**
     * Muestra los tickets del usuario logueado.
     *
     * @return View Vista con los tickets del usuario.
     */
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


    /**
     * Valida un ticket.
     *
     * @param int $id ID del ticket.
     *
     * @return Factory|Application|object|View Vista de ticket válido.
     */
    public function validar($id)
    {
        $ticket = Ticket::find($id);
        $evento = Evento::find($ticket->idEvent);

        if ($evento) {
            return view('tickets.valido', ['ticket' => $evento]);
        } else {
            return redirect()->route('eventos')->with('error', 'Ticket no válido.');
        }
    }


    /**
     * Descarga un ticket en formato PDF.
     *
     * @param int $ticketId ID del ticket.
     *
     * @return RedirectResponse|Response Redirecciona a la vista de tickets.
     */
    public function download($ticketId)
    {
        $ticket = Ticket::findOrFail($ticketId);

        $evento = Evento::find($ticket->idEvent);

        if (!$evento) {
            return redirect()->route('tickets.index')->with('error', 'Evento no encontrado.');
        }

        $pdf = PDF::loadView('tickets.pdf', compact('ticket', 'evento'));

        return $pdf->download('ticket_'.$evento->nombre.'.pdf');
    }

}
