<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class TicketApiController extends Controller
{
    public function index()
    {
        return response()->json(Ticket::all(), 200);
    }


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

        Cache::put($cacheKey, $ticket, 20);

        Cache::forget('tickets.all');

        return response()->json(['message' => 'Ticket successfully returned', 'ticket' => $ticket], 200);
    }


}
