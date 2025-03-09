<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;


class TicketApiController extends Controller
{
    /**
     * Muestra el listado de tickets.
     *
     * @return JsonResponse con el listado de tickets.
     */
    public function index()
    {
        return response()->json(Ticket::all(), 200);
    }



    /**
     * Muestra el detalle de un ticket.
     *
     * @param int $id del ticket.
     *
     * @return JsonResponse con los detalles del ticket.
     */
    public function show($id)
    {
        $cacheKey = "ticket_{$id}";

        $ticket = Cache::get($cacheKey);

        if (!$ticket) {
            $ticket = Ticket::find($id);

            if (!$ticket) {
                return response()->json(['message' => 'Ticket not found'], 404);
            }

            Cache::put($cacheKey, $ticket, 20);
        }

        return response()->json($ticket, 200);
    }



    /**
     * Crea un ticket.
     *
     * @param Request $request para la peticion de creacion.
     *
     * @return JsonResponse con el ticket creado.
     */
    public function store(Request $request)
    {
        $request->validate([
            'idEvent' => 'required',
            'idClient' => 'required',
            'price' => 'required|numeric|min:0',
            'isReturned' => 'boolean'
        ]);

        $ticket = Ticket::create($request->only(['idEvent', 'idClient', 'price', 'isReturned']));

        return response()->json($ticket, 201);
    }


    /**
     * Actualiza un ticket.
     *
     * @param int $id del ticket.
     *
     * @return JsonResponse con el ticket actualizado.
     */
    public function destroy($id)
    {
        $cacheKey = "ticket_{$id}";


        $ticket = Cache::get($cacheKey);

        if (!$ticket) {
            $ticket = Ticket::find($id);

            if (!$ticket) {
                return response()->json(['message' => 'Ticket not found'], 404);
            }
        }

        if ($ticket->isReturned) {
            return response()->json(['message' => 'This ticket has already been returned'], 400);
        }

        $ticket->update(['isReturned' => true]);

        Cache::forget($cacheKey);

        return response()->json(['message' => 'Ticket successfully returned', 'ticket' => $ticket], 200);
    }

}
