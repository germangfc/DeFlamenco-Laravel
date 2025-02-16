<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(){
        return response()->json(Ticket::all(),200);
    }

    public function show($id){
        $ticket = Ticket::find($id);
        if(!$ticket){
            return response()->json(['message' => 'Ticket not found'],404);
        }
        return response()->json($ticket,200);
    }

    public function store(Request $request){
        $request->validate([
            'idEvent'=>"required",
            'idClient' => 'required',
            'price' => 'required',
            'isReturned' => 'boolean'
        ]);

        $ticket = Ticket::create($request->all(['idEvent','idClient','price','isReturned']));

        return response()->json($ticket,201);
    }

    public function delete(Request $request, $id)
    {
        $ticket = Ticket::find($id);
        if (!$ticket) {
            return response()->json(['message' => 'Ticket not found'], 404);
        }

        $ticket->update($request->all(['isReturned']));

        return response()->json($ticket, 200);
    }
}
