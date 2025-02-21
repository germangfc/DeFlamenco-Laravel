<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class VentasApiController extends Controller
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
            $request->validate([
                'guid' => 'required|unique:ventas',
                'lineasVenta' => 'array|min:1'
            ]);
            $request->lineasVenta->validate([
                'idTicket' => "required|unique|exists:Ticket",
                'precioVentaTicket'=> 'required|numeric'
            ]);

            $venta = new Venta($request->all());
            $venta->save();
            return response()->json($venta, 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al guardar la venta'], 400);
        }

    }
    public function update(Request $request, $id){
        try {
            $request->validate([
                'guid' => "required|unique:ventas,guid,{$id}",
                'lineasVenta' => 'array|min:1'
            ]);
            $request->lineasVenta->validate([
                'idTicket' => "required|unique|exists:tickets,{$id}",
                'precioVentaTicket'=> 'required|numeric'
            ]);
            $venta = Venta::find($id);
            if (!$venta) {
                return response()->json(['message' => 'Venta not Found'], 404);
            }
            $venta->update($request->all());

            return response()->json($venta, 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);

        } catch (Exception $e) {
            return response()->json(['error' => 'Error al guardar la venta'], 400);
        }
    }

    public function destroy($id){
        $venta = Venta::find($id);
        if (!$venta) {
            return response()->json(['message' => 'Venta not Found'], 404);
        }
        $venta->delete();
        return response()->json(['message' => 'Venta eliminada'], 204);
    }
}
