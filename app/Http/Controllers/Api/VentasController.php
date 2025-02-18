<?php

namespace App\Http\Controllers\Api;

use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class VentasController extends Controller
{
    public function index()
    {
        return response()->json(Venta::all(), 200);
    }

    public function show($id){
        $venta = Venta::find($id);
        if (!$venta) {
            return response()->json(['message' => 'Venta not Found'], 404);
        }
        return response()->json($venta, 200);

    }
    public function store(Request $request){
        try {
            $request->lineasVenta->validate([
                'guid' => "required|unique:ventas,guid,{$id}",
                'idTicket' => "required|unique|exists:Ticket,{$id}",
                'precioVentaTicket'=> 'required|numeric'
            ]);
            $request->validate([
                'guid' => 'required|unique:ventas',
                'lineasVenta' => 'array|min:1'
            ]);

            $venta = new Venta($request->all());
            $venta->save();
            return response()->json($venta, 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        }

    }
    public function update(Request $request, $id){
        try {
            $request->lineasVenta->validate([
                'guid' => "required|unique:ventas,guid,{$id}",
                'idTicket' => "required|unique|exists:Ticket,{$id}",
                'precioVentaTicket'=> 'required|numeric'
            ]);
            $request->validate([
                'guid' => "required|unique:ventas,guid,{$id}",
                'lineasVenta' => 'array|min:1'
            ]);
            $venta = Venta::find($id);

        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);

        }
    }
}
