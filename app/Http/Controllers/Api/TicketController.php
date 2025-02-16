<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        return response()->json(Ticket::all(), 200);
    }

    public function show($id)
    {
        $ticket = Ticket::find($id);
        if (!$ticket) {
            return response()->json(['message' => 'Ticket not found'], 404);
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
        $ticket = Ticket::find($id);
        if (!$ticket) {
            return response()->json(['message' => 'Ticket not found'], 404);
        }

        $ticket->update(['isReturned' => true]);

        return response()->json(['ticket' => $ticket], 200);
    }
}
