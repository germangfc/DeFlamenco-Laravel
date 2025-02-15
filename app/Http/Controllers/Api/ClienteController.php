<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{

    public function index()
    {
        return response()->json(Cliente::all(), 200);
    }

    public function show($id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        return response()->json($cliente, 200);
    }


    public function store(Request $request)
    {

        $request->validate([
            "user_id"=>"required",
            'nombre' => 'required|string|max:255',
            'dni' => 'required|string|max:20|unique:clientes,dni',
            'foto_dni' => 'nullable|string',
            'lista_entradas' => 'nullable|array',
            'lista_entradas.*' => 'string'
        ]);

        $cliente = Cliente::create($request->all(['user_id','nombre','dni','foto_dni','lista_entradas']));

        return response()->json($cliente, 201);
    }




    public function update(Request $request, $id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'dni' => 'required|string|max:20|unique:clientes,dni',
            'foto_dni' => 'nullable|string'
        ]);

        $cliente->update($request->all(['nombre','dni','foto_dni']));

        return response()->json($cliente, 200);
    }


    public function destroy($id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $cliente->delete();
        return response()->json(['message' => 'Cliente eliminado'], 200);
    }
}
