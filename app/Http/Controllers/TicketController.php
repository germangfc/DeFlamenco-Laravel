<?php

namespace App\Http\Controllers;

use App\Models\Ticket;

class TicketController extends Controller
{
    public function index()
    {
        $idClient = auth()->user()->id;
        $tickets = Ticket::where('idClient', $idClient)->paginate(10);

        return view('tickets.index', compact('tickets'));
    }
}
