<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use PHPUnit\Exception;


class EmpresaControllerApi extends Controller
{


    /**
     * Muestra el listado de empresas.
     *
     * @param Request $request para la peticion de busqueda.
     *
     * @return JsonResponse con el listado de empresas.
     */
    public function getAll(Request $request){
        $empresas = Empresa::search($request->name)->orderBy('id', 'ASC')->paginate(5);

        return response()->json($empresas, status: 200);
    }


    /**
     * Muestra el detalle de una empresa.
     *
     * @param int $id del cliente.
     *
     * @return JsonResponse con los detalles del cliente.
     */
    public function getById($id){
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


    /**
     * Muestra el detalle de una empresa por su nombre.
     *
     * @param string $nombre del cliente.
     *
     * @return JsonResponse con los detalles del cliente.
     */
    public function getByNombre($nombre){
        $cacheKey = "empresa_nombre_{$nombre}";

        $empresa = Cache::get($cacheKey);

        if (!$empresa) {
            $empresa = Empresa::where('name', $nombre)->first();

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


    /**
     * Muestra el detalle de una empresa por su cif.
     *
     * @param string $cif del cliente.
     *
     * @return JsonResponse con los detalles del cliente.
     */
    public function getByCif($cif){
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


    /**
     * Crea una nueva empresa.
     *
     * @param Request $request con los datos de la empresa a crear.
     *
     * @return JsonResponse con los detalles de la empresa creada.
     */
    public function create(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'cif'            => ['required', 'regex:/^[A-HJNP-SUVW][0-9]{7}[0-9A-J]$/'],
                'name'         => ['required', 'max:255','unique:empresas,name'],
                'direccion'      => 'required|max:255',
                'cuentaBancaria' => ['required', 'regex:/^ES\d{2}\s?\d{4}\s?\d{4}\s?\d{2}\s?\d{10}$/'],
                'telefono'       => ['required', 'regex:/^(\+34|0034)?[679]\d{8}$/'],
                'email'          => 'required|string|email|unique:empresas,email',
                'password'       => 'required|string|min:8',
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error de validación', 'detalles' => $e->errors()], 422);
        }

        try {
            $user = new User();
            $user->name     = $validatedData['name'];
            $user->email    = $validatedData['email'];
            $user->password = Hash::make($validatedData['password']);
            $user->tipo     = 'empresa';
            $user->save();
            $empresa = new Empresa();
            $empresa->cif           = $validatedData['cif'];
            $empresa->name        = $validatedData['name'];
            $empresa->direccion     = $validatedData['direccion'];
            $empresa->cuentaBancaria = $validatedData['cuentaBancaria'];
            $empresa->telefono      = $validatedData['telefono'];
            $empresa->email         = $validatedData['email'];
            $empresa->usuario_id    = $user->id;
            $empresa->save();

            return response()->json(['message' => 'Empresa creada', 'empresa' => $empresa], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error en la base de datos', 'detalles' => $e->getMessage()], 400);
        }
    }


    /**
     * Actualiza una empresa.
     *
     * @param int $id de la empresa a actualizar.
     * @param Request $request con los datos de la empresa a actualizar.
     *
     * @return JsonResponse con la empresa actualizada.
     */
    public function update($id, Request $request)
    {
        $empresa = Empresa::find($id);
        if (!$empresa) {
            return response()->json(['message' => 'Empresa no encontrada'], 404);
        }

        $user = User::find($empresa->usuario_id);
        if (!$user) {
            return response()->json(['message' => 'Usuario asociado a la empresa no encontrado'], 404);
        }


        $validatedData = $request->validate([
            'cif'            => ['nullable', 'regex:/^[A-HJNP-SUVW][0-9]{7}[0-9A-J]$/'],
            'name'         => ['nullable', 'max:255', Rule::unique('empresas')],
            'direccion'      => 'nullable|max:255',
            'cuentaBancaria' => ['nullable', 'regex:/^ES\d{2}\s?\d{4}\s?\d{4}\s?\d{2}\s?\d{10}$/'],
            'telefono'       => ['nullable', 'regex:/^(\+34|0034)?[679]\d{8}$/'],
            'email'          => ['nullable', 'string', 'email', Rule::unique('empresas', 'email')],
            'password'       => 'nullable|string|min:8',
        ]);



        $empresa->update($request->except(['email', 'nombre', 'password']));

        $updated = false;
        if ($request->filled('name') && $user->name !== $request->nombre) {
            $user->name = $request->name;
            $empresa->name = $request->name;
            $updated = true;
        }
        if ($request->filled('email') && $user->email !== $request->email) {
            $user->email = $request->email;
            $empresa->email = $request->email;
            $updated = true;
        }
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $updated = true;
        }

        if ($updated) {
            $user->save();
            $empresa->save();
        }

        Cache::forget("empresa_{$empresa->id}");
        Cache::forget("empresa_cif_{$empresa->cif}");
        Cache::forget("user_{$user->id}");

        Cache::put("empresa_{$empresa->id}", $empresa, 20);
        Cache::put("empresa_cif_{$empresa->cif}", $empresa, 20);
        Cache::put("user_{$user->id}", $user, 20);

        return response()->json([
            'message' => 'Empresa y usuario actualizados con éxito',
            'empresa' => $empresa,
        ], 200);
    }




    /**
     * Elimina una empresa.
     *
     * @param int $id de la empresa a eliminar.
     *
     * @return JsonResponse con el resultado de la eliminación.
     */
    public function destroy($id)
    {
        $empresaCacheKey = "empresa_{$id}";
        $empresa = Cache::get($empresaCacheKey);

        if ($empresa === null) {
            $empresa = Empresa::find($id);
            if ($empresa === null) {
                return response()->json(['message' => 'Empresa no encontrada'], 404);
            }
        }

        $userCacheKey = "user_{$empresa->usuario_id}";
        $user = Cache::get($userCacheKey);
        if ($user === null) {
            $user = User::find($empresa->usuario_id);
            if ($user === null) {
                return response()->json(['message' => 'Usuario asociado a la empresa no encontrado'], 404);
            }
        }

        $empresa->update(['isDeleted' => true]);
        $user->update(['isDeleted' => true]);

        Cache::forget($empresaCacheKey);
        Cache::forget($userCacheKey);

        return response()->json(['message' => 'Empresa y usuario eliminados correctamente'], 204);
    }

}
