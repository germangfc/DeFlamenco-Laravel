<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoginControllerApi extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        // Validación de los datos de entrada
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Intentar autenticar al usuario
        $credentials = $request->only('email', 'password');

        if (!$token = Auth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Devolver el token en la respuesta
        return response()->json([
            'token' => $token,
        ]);
    }
}
