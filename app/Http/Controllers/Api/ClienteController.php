<?php

namespace App\Http\Controllers\Api;

use App\Dto\Cliente\ClienteResponse;
use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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

    public function searchByDni($dni)
    {
        $cliente = Cliente::findByDni($dni)->first();

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $user = User::find($cliente->user_id);
        return response()->json(new ClienteResponse($user, $cliente), 200);
    }

    public function searchByEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email'
        ]);

        $user = User::findByEmail($request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $cliente = Cliente::findByUserId($user->id)->first();

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado para este usuario'], 404);
        }

        return response()->json([
            new ClienteResponse($user,$cliente)
        ], 200);
    }


    public function store(Request $request)
    {
        try {
            // Validación de los datos del usuario
            $validatedUserData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users,email',
                'password' => 'required|string|min:8'
            ]);

            $validatedClientData = $request->validate([
                'dni' => 'required|string|max:20|unique:clientes,dni'
            ]);

            $user = User::create([
                'name' => $validatedUserData['name'],
                'email' => $validatedUserData['email'],
                'password' => Hash::make($validatedUserData['password']),
            ]);

            $cliente = Cliente::create([
                'user_id' => $user->id,
                'dni' => $validatedClientData['dni']
            ]);

            return response()->json([
                new ClienteResponse($user,$cliente),
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

        $validatedData = $request->validate([
            'dni' => 'nullable|string|max:20|unique:clientes,dni,' . $cliente->id,
            'foto_dni' => 'nullable|string',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|unique:users,email,' . $cliente->user_id,
            'password' => 'nullable|string|min:8',
        ]);

        if ($request->has('dni')) {
            $cliente->dni = $validatedData['dni'];
        }
        if ($request->has('foto_dni')) {
            $cliente->foto_dni = $validatedData['foto_dni'];
        }
        $cliente->save();

        $user = User::find($cliente->user_id);

        if (!$user) {
            return response()->json(['message' => 'User no encontrado'], 404);
        }

        if ($request->has('name')) {
            $user->name = $validatedData['name'];
        }
        if ($request->has('email')) {
            $user->email = $validatedData['email'];
        }
        if ($request->has('password')) {
            $user->password = Hash::make($validatedData['password']);
        }

        $user->save();


        return response()->json(new ClienteResponse($user, $cliente), 200);
    }


    public function destroy($id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $user = User::find($cliente->user_id);

        $cliente->is_deleted = true;
        $cliente->save();


        $user = User::find($cliente->user_id);

        if (!$user) {
            return response()->json(['message' => 'User no encontrado'], 404);
        }

        $user->is_deleted = true;
        $user->save();

        return response()->json(['message' => 'Cliente marcado como eliminado'], 200);
    }

    public function uploadDni(Request $request, $clienteId)
    {
        $request->validate([
            'foto_dni' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $cliente = Cliente::findOrFail($clienteId);

        if (!empty($cliente->foto_dni)) {
            Storage::disk('public')->delete('images/' . $cliente->foto_dni);
        }


        if ($request->hasFile('foto_dni')) {
            $image = $request->file('foto_dni');

            $customName = 'dni_' . $cliente->dni . '.' . $image->getClientOriginalExtension();

            $image->storeAs('images', $customName, 'public');

            $cliente->foto_dni = $customName;
            $cliente->save();

            return response()->json([
                'message' => 'Imagen subida correctamente',
                'path' => $customName
            ]);
        }

        return response()->json(['message' => 'No se ha enviado ningún archivo'], 400);
    }



}
