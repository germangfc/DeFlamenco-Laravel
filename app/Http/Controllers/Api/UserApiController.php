<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
class UserApiController extends Controller
{

    /**
     * Muestra el listado de usuarios.
     *
     * @return JsonResponse con el listado de usuarios.
     */
    public function index()
    {
        return response()->json(User::all());
    }


    /**
     * Crea un usuario.
     *
     * @param Request $request para la peticion de creacion.
     *
     * @return JsonResponse con el usuario creado.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users,email',
                'password' => 'required|string|min:8',
                'tipo' => 'required|in:cliente,admin,empresa',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'tipo' => $request->tipo,
            ]);

            return response()->json($user, 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $e->errors()
            ], 422);
        }
    }



    /**
     * Muestra el detalle de un usuario.
     *
     * @param string $id del usuario.
     *
     * @return JsonResponse con los detalles del usuario.
     */
    public function show(string $id)
    {
        $cacheKey = "user_{$id}";

        $user = Cache::get($cacheKey);

        if (!$user) {
            $user = User::find($id);

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            Cache::put($cacheKey, $user, 20);
        }

        return response()->json($user);
    }



    /**
     * Actualiza un usuario.
     *
     * @param Request $request para la peticion de actualizacion.
     * @param string $id del usuario.
     *
     * @return JsonResponse con el usuario actualizado.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8'
        ]);

        if (isset($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        $user->update($validatedData);

        $cacheKey = "user_{$id}";
        Cache::put($cacheKey, $user, 20);

        return response()->json($user);
    }



    /**
     * Elimina un usuario.
     *
     * @param string $id del usuario a eliminar.
     *
     * @return JsonResponse con el resultado de la eliminación.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        $cacheKey = "user_{$id}";
        Cache::forget($cacheKey);

        return response()->json(['message' => 'User deleted']);
    }
}
