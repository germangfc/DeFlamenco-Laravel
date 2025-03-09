<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoginControllerApi extends Controller
{

    /**
     * @param Request $request
     *
     * @return JsonResponse con el token de autenticacion.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['error' => 'User not Found'], 404);
        }
        if ($user->tipo !== 'admin') {
            return response()->json(['error' => 'Unauthorized, you are not an admin'], 401);
        }
        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized, wrong credentials'], 401);
        }

        return response()->json([
            'token' => $token,
        ]);
    }
}
