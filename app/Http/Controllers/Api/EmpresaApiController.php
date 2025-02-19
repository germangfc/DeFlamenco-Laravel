<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Exception;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class EmpresaApiController extends Controller
{
    public function getAll(Request $request){
        $empresas = Empresa::search($request->name)->orderBy('id', 'ASC')->paginate(5);

        return response()->json($empresas, status: 200);
    }

    public function getById($id)
    {
        $cacheKey = "empresa_{$id}";

        $empresa = Cache::get($cacheKey);

        if (!$empresa) {
            $empresa = Empresa::find($id);

            if ($empresa) {
                Cache::put($cacheKey, $empresa, 20);
            }
        }

        if ($empresa) {
            return response()->json($empresa, 200);
        } else {
            return response()->json(['message' => 'Empresa no encontrada'], 404);
        }
    }


    public function getByNombre($nombre)
    {
        $cacheKey = "empresa_nombre_{$nombre}";

        $empresa = Cache::get($cacheKey);

        if (!$empresa) {
            $empresa = Empresa::where('nombre', $nombre)->first();

            if ($empresa) {
                Cache::put($cacheKey, $empresa, 20);
            }
        }

        if ($empresa) {
            return response()->json($empresa, 200);
        } else {
            return response()->json(['message' => 'Empresa no encontrada'], 404);
        }
    }


    public function getByCif($cif)
    {
        $cacheKey = "empresa_cif_{$cif}";

        $empresa = Cache::get($cacheKey);

        if (!$empresa) {
            $empresa = Empresa::where('cif', $cif)->first();

            if ($empresa) {
                Cache::put($cacheKey, $empresa, 20);
            }
        }

        if ($empresa) {
            return response()->json($empresa, 200);
        } else {
            return response()->json(['message' => 'Empresa no encontrada'], 404);
        }
    }


    public function create(Request $request){
        $request->validate([
            'cif'=> 'required|regex:/^[A-HJNP-SUVW][0-9]{7}[0-9A-J]$/',
            'nombre'=> 'required|max:255',
            'direccion'=> 'required|max:255',
            'cuentaBancaria'=>'required|regex:/^ES\d{2}\s?\d{4}\s?\d{4}\s?\d{2}\s?\d{10}$/',
            'telefono'=> 'required|regex:/^(\+34|0034)?[679]\d{8}$/',
        ]);
        $validatedUserData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8'
        ]);

        try {
            $user = User::create([
                'name' => $validatedUserData['name'],
                'email' => $validatedUserData['email'],
                'password' => Hash::make($validatedUserData['password']),
            ]);

            $empresa = Empresa::create([
                $request->all(),
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            $user->save();
            $empresa->save();
            return response()->json($empresa, status: 201);
        }catch (Exception $e){
            return response()->json(['message' => 'Error al crear la empresa: '. $e->getMessage()], status: 400);
        }
    }

    public function update($id, Request $request)
    {
        $request->validate([
            'cif'=> 'nullable|regex:/^[A-HJNP-SUVW][0-9]{7}[0-9A-J]$/',
            'nombre'=> 'nullable|max:255',
            'direccion'=> 'nullable|max:255',
            'cuentaBancaria'=>'nullable|regex:/^ES\d{2}\s?\d{4}\s?\d{4}\s?\d{2}\s?\d{10}$/',
            'telefono'=> 'nullable|regex:/^(\+34|0034)?[679]\d{8}$/',
        ]);

        $validatedUserData = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|unique:users,email',
            'password' => 'nullable|string|min:8'
        ]);

        try {
            $empresa = Empresa::find($id);
            if (!$empresa) {
                return response()->json(['message' => 'Empresa no encontrada'], 404);
            }

            $user = User::find($empresa->user_id);
            if (!$user) {
                return response()->json(['message' => 'Usuario asociado a la empresa no encontrado'], 404);
            }

            $empresa->update($request->all());
            $empresa->save();

            $user->update($validatedUserData);
            $user->save();

            Cache::forget("empresa_{$empresa->id}");
            Cache::forget("empresa_cif_{$empresa->cif}");
            Cache::forget("user_{$user->id}");

            Cache::put("empresa_{$empresa->id}", $empresa, 20);
            Cache::put("empresa_cif_{$empresa->cif}", $empresa, 20);
            Cache::put("user_{$user->id}", $user, 20);

            return response()->json($empresa, 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al actualizar la empresa: '. $e->getMessage()], 400);
        }
    }


    public function delete($id)
    {
        $empresaCacheKey = "empresa_{$id}";
        $empresa = Cache::get($empresaCacheKey);

        if (!$empresa) {
            $empresa = Empresa::find($id);
            if (!$empresa) {
                return response()->json(['message' => 'Empresa no encontrada'], 404);
            }
            Cache::put($empresaCacheKey, $empresa, 20);
        }

        $userCacheKey = "user_{$empresa->user_id}";
        $user = Cache::get($userCacheKey);

        if (!$user) {
            $user = User::find($empresa->user_id);
            if (!$user) {
                return response()->json(['message' => 'Usuario asociado a la empresa no encontrado'], 404);
            }
            Cache::put($userCacheKey, $user, 20);
        }

        $empresa->isDeleted = true;
        $user->isDeleted = true;

        $empresa->save();
        $user->save();

        Cache::forget($empresaCacheKey);
        Cache::forget($userCacheKey);

        return response()->json(['message' => 'Empresa y usuario eliminados correctamente'], 204);
    }


}
