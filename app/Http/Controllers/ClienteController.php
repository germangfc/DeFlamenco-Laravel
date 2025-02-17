<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{


    public function getAll()
    {
        $clientes = Cliente::all();

        return view('card', compact('clientes'));
    }
/*
    public function getById($id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        return response()->json($cliente, 200);
    }


    public function store(Request $request)
    {

        $validated = $request->validate(array(
            'user_id' => 'required|string',
            'nombre' => 'required|string|max:255',
            'dni' => 'required|string|max:20|unique:clientes,dni',
            'foto_dni' => 'nullable|string',
            'lista_entradas' => 'nullable|array'
        ));

        $cliente = Cliente::create($validated);

        return response()->json($cliente, 201);
    }




    public function update(Request $request, $id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $validated = $request->validate([
            'nombre' => 'string|max:255',
            'dni' => "string|max:20|unique:clientes,dni,$id",
            'foto_dni' => 'nullable|string'
        ]);

        $cliente->update($validated);

        return response()->json($cliente, 200);
    }


    public function delete($id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $cliente->delete();
        return response()->json(['message' => 'Cliente eliminado'], 200);
    }
    */

}
