<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ActualizacionDatos;
use App\Mail\ClienteBienvenido;
use App\Mail\EliminacionCuenta;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;
use function PHPUnit\Framework\logicalAnd;


class ClienteApiController extends Controller
{

    /**
     * Muestra el listado de clientes.
     *
     * @param Request $request para la peticion de busqueda.
     *
     * @return JsonResponse con el listado de clientes.
     */

    public function index(Request $request)
    {
        $clientes = Cliente::search($request->user_id)->orderBy('id', 'ASC')->paginate(5);
        return response()->json($clientes, 200);
    }

    /**
     * Muestra el detalle de un cliente.
     *
     * @param int $id del cliente.
     *
     * @return JsonResponse con los detalles del cliente.
     */

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

    /**
     * Busca un cliente por su email.
     *
     * @param Request $request con el email del cliente.
     *
     * @return JsonResponse con los detalles del cliente.
     */

    public function searchByEmail(Request $request)
    {
        $userCacheKey = "user_email_{$request->email}";
        $clienteCacheKey = "cliente_user_{$request->email}";

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

        return response()->json( $cliente, 200);
    }



    /**
     * Almacena un nuevo cliente en la base de datos.
     *
     * @param Request $request con los datos del cliente.
     *
     * @return JsonResponse con el cliente creado.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users,email',
                'password' => 'required|string|min:8',
            ]);


            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);

            $cliente = Cliente::create([
                'user_id' => $user->id,
                'avatar'=>"avatardefault.jpg"
            ]);

            Mail::to($user->email)->send(new ClienteBienvenido($cliente, $user));


            return response()->json([
                $cliente,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }



    /**
     * Actualiza un cliente.
     *
     * @param Request $request con los datos del cliente a actualizar.
     *
     * @param int $id del cliente a actualizar.
     *
     * @return JsonResponse con el resultado de la actualización.
     */
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
            'avatar' => 'nullable|string',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|unique:users,email,' . $cliente->user_id,
            'password' => 'nullable|string|min:8',
        ]);


        if ($request->has('avatar')) {
            $cliente->avatar = $validatedData['avatar'];
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
        return response()->json( $cliente, 200);
    }



    /**
     * Elimina un cliente.
     *
     * @param int $id ID del cliente a eliminar.
     *
     * @return JsonResponse con el resultado de la eliminación.
     */
    public function destroy($id)
    {
        try {
            $clienteCacheKey = "cliente_{$id}";
            $cliente = Cache::get($clienteCacheKey);

            if (!$cliente) {
                $cliente = Cliente::find($id);
                if (!$cliente) {
                    return response()->json(['message' => 'Cliente no encontrado'], 404);
                }
            }

            $userCacheKey = "user_{$cliente->user_id}";
            $user = Cache::get($userCacheKey);

            if (!$user) {
                $user = User::find($cliente->user_id);
                if (!$user) {
                    return response()->json(['message' => 'Usuario no encontrado'], 404);
                }
            }

            $cliente->is_deleted = true;
            $user->isDeleted = true;

            $cliente->save();
            $user->save();

            Cache::forget($clienteCacheKey);
            Cache::forget($userCacheKey);

            Mail::to($user->email)->send(new EliminacionCuenta($user));

            return response()->json(['message' => 'Cliente marcado como eliminado'], 200);

        } catch (\Exception $e) {
            Log::error('Error en el método destroy: ' . $e->getMessage());
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }



}
