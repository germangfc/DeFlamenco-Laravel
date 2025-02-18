<?php

namespace App\Http\Controllers\Api;

use App\Dto\Cliente\ClienteResponse;
use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;


class ClienteApiController extends Controller
{

    public function index()
    {
        return response()->json(Cliente::all(), 200);
    }


    public function show($id)
    {
        $cacheKey = "cliente_{$id}";

        $cliente = Cache::get($cacheKey);

        if (!$cliente) {
            $cliente = Cliente::find($id);

            if (!$cliente) {
                return response()->json(['message' => 'Client not found'], 404);
            }

            Cache::put($cacheKey, $cliente, 20);
        }

        return response()->json($cliente, 200);
    }



    public function store(Request $request)
    {
        try {
            $validatedUserData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users,email',
                'password' => 'required|string|min:8'
            ]);

            $validatedClientData = $request->validate([
                'dni' => 'required|string|max:20|unique:clientes,dni',
                'foto_dni' => 'nullable|string',
                'lista_entradas' => 'nullable|array',
                'lista_entradas.*' => 'string'
            ]);

            $user = User::create([
                'name' => $validatedUserData['name'],
                'email' => $validatedUserData['email'],
                'password' => Hash::make($validatedUserData['password']),
            ]);

            $cliente = Cliente::create([
                'user_id' => $user->id,
                'dni' => $validatedClientData['dni'],
                'foto_dni' => $validatedClientData['foto_dni'],
                'lista_entradas' => $validatedClientData['lista_entradas']
            ]);

            return response()->json([
                new ClienteResponse($user,$cliente)
            ], 201);

        } catch (ValidationException $e) {

            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }


    public function update(Request $request, $id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $request->validate([
            'dni' => 'required|string|max:20|unique:clientes,dni',
            'foto_dni' => 'nullable|string'
        ]);

        $cliente->update($request->only(['dni', 'foto_dni']));

        $user = User::find($cliente->user_id);

        $cacheKey = "cliente_{$cliente->id}_response";
        $response = new ClienteResponse($user, $cliente);

        Cache::put($cacheKey, $response, 20);

        return response()->json($response, 200);
    }



    public function destroy($id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $cliente->delete();

        $cacheKey = "cliente_{$id}_response";
        Cache::forget($cacheKey);

        return response()->json(['message' => 'Cliente eliminado'], 200);
    }
}
