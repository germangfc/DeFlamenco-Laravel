<?php

namespace App\Http\Controllers\Api;

use App\Dto\Cliente\ClienteResponse;
use App\Http\Controllers\Controller;
use App\Mail\ActualizacionDatos;
use App\Mail\ClienteBienvenido;
use App\Mail\EliminacionCuenta;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;
use function PHPUnit\Framework\logicalAnd;


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

    public function searchByDni($dni)
    {
        $clienteCacheKey = "cliente_dni_{$dni}";
        $cliente = Cache::get($clienteCacheKey);

        if (!$cliente) {
            $cliente = Cliente::findByDni($dni)->first();

            if (!$cliente) {
                return response()->json(['message' => 'Cliente no encontrado'], 404);
            }

            Cache::put($clienteCacheKey, $cliente, 60);
        }

        $userCacheKey = "user_{$cliente->user_id}";
        $user = Cache::get($userCacheKey);

        if (!$user) {
            $user = User::find($cliente->user_id);
            if ($user) {
                Cache::put($userCacheKey, $user, 60);
            }
        }

        return response()->json(new ClienteResponse($user, $cliente), 200);
    }


    public function searchByEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email'
        ]);

        // Definir las claves de caché para el usuario y el cliente
        $userCacheKey = "user_email_{$request->email}";
        $clienteCacheKey = "cliente_user_{$request->email}";

        // Buscar en la caché el usuario
        $user = Cache::get($userCacheKey);

        if (!$user) {
            $user = User::findByEmail($request->email)->first();

            if (!$user) {
                return response()->json(['message' => 'Usuario no encontrado'], 404);
            }

            Cache::put($userCacheKey, $user, 60);
        }

        $cliente = Cache::get($clienteCacheKey);

        if (!$cliente) {
            $cliente = Cliente::findByUserId($user->id)->first();

            if (!$cliente) {
                return response()->json(['message' => 'Cliente no encontrado para este usuario'], 404);
            }

            Cache::put($clienteCacheKey, $cliente, 60);
        }

        return response()->json([new ClienteResponse($user, $cliente)], 200);
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

            Mail::to($user->email)->send(new ClienteBienvenido($cliente, $user));


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
        $clienteCacheKey = "cliente_{$id}";
        $cliente = Cache::get($clienteCacheKey);

        if (!$cliente) {
            $cliente = Cliente::find($id);

            if (!$cliente) {
                return response()->json(['message' => 'Cliente no encontrado'], 404);
            }

            Cache::put($clienteCacheKey, $cliente, 20);
        }

        $userCacheKey = "user_{$cliente->user_id}";
        $user = Cache::get($userCacheKey);

        if (!$user) {
            $user = User::find($cliente->user_id);

            if (!$user) {
                return response()->json(['message' => 'Usuario no encontrado'], 404);
            }

            Cache::put($userCacheKey, $user, 20);
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
        if ($request->has('name')) {
            $user->name = $validatedData['name'];
        }
        if ($request->has('email')) {
            $user->email = $validatedData['email'];
        }
        if ($request->has('password')) {
            $user->password = Hash::make($validatedData['password']);
        }

        $cliente->save();
        $user->save();

        Cache::put($clienteCacheKey, $cliente, 20);
        Cache::put($userCacheKey, $user, 20);

        Mail::to($user->email)->send(new ActualizacionDatos($user));
        return response()->json(new ClienteResponse($user, $cliente), 200);
    }


    public function destroy($id)
    {
        $clienteCacheKey = "cliente_{$id}";
        $cliente = Cache::get($clienteCacheKey);

        if (!$cliente) {
            $cliente = Cliente::find($id);
        }

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $userCacheKey = "user_{$cliente->user_id}";
        $user = Cache::get($userCacheKey);

        if (!$user) {
            $user = User::find($cliente->user_id);
        }
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $cliente->is_deleted = true;
        $user->is_Deleted = true;

        $cliente->save();
        $user->save();

        Cache::forget($clienteCacheKey);
        Cache::forget($userCacheKey);

        Mail::to($user->email)->send(new EliminacionCuenta($user));

        return response()->json(['message' => 'Cliente marcado como eliminado'], 200);
    }

    public function uploadDni(Request $request, $clienteId)
    {
        $request->validate([
            'foto_dni' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $cliente = Cliente::find($clienteId);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        if (!empty($cliente->foto_dni)) {
            Storage::disk('public')->delete('images/' . $cliente->foto_dni);
        }

        $image = $request->file('foto_dni');

        $customName = 'dni_' . $cliente->dni . '.' . $image->getClientOriginalExtension();

        $image->storeAs('images', $customName, 'public');

        $cliente->foto_dni = $customName;
        $cliente->save();

        return response()->json([
            'message' => 'DNI actualizado correctamente',
            'path' => $customName
        ]);
    }



}
