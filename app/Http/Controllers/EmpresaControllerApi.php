<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Psy\Util\Json;

class EmpresaControllerApi extends Controller
{
    public function getAll(Request $request){
        $empresas = Empresa::search($request->name)->orderBy('id', 'ASC')->paginate(5);

        return response()->json($empresas, status: 200);
    }

    public function getById($id){
        $empresa = Empresa::find($id);
        if($empresa){
            return response()->json($empresa, status: 200);
        } else {
            return response()->json(['message' => 'Empresa no encontrada'], status: 404);
        }
    }

    public function getByNombre($nombre){
        $empresa = Empresa::where('nombre', $nombre)->first();
        if($empresa){
            return response()->json($empresa, status: 200);
        } else {
            return response()->json(['message' => 'Empresa no encontrada'], status: 404);
        }
    }

    public function getByCif($cif){
        $empresa = Empresa::where('cif', $cif)->first();
        if($empresa){
            return response()->json($empresa, status: 200);
        } else {
            return response()->json(['message' => 'Empresa no encontrada'], status: 404);
        }
    }

    public function create(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'cif'            => ['required', 'regex:/^[A-HJNP-SUVW][0-9]{7}[0-9A-J]$/'],
                'nombre'         => 'required|max:255',
                'direccion'      => 'required|max:255',
                'cuentaBancaria' => ['required', 'regex:/^ES\d{2}\s?\d{4}\s?\d{4}\s?\d{2}\s?\d{10}$/'],
                'telefono'       => ['required', 'regex:/^(\+34|0034)?[679]\d{8}$/'],
                'email'          => 'required|string|email|unique:users,email',
                'password'       => 'required|string|min:8',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Error de validación', 'detalles' => $e->errors()], 422);
        }

        try {
            $user = new User();
            $user->name     = $validatedData['nombre'];
            $user->email    = $validatedData['email'];
            $user->password = Hash::make($validatedData['password']);
            $user->save();

            $empresa = new Empresa();
            $empresa->cif           = $validatedData['cif'];
            $empresa->nombre        = $validatedData['nombre'];
            $empresa->direccion     = $validatedData['direccion'];
            $empresa->cuentaBancaria = $validatedData['cuentaBancaria'];
            $empresa->telefono      = $validatedData['telefono'];
            $empresa->email         = $validatedData['email'];
            $empresa->usuario_id    = $user->id;
            $empresa->save();

            return response()->json(['message' => 'Empresa creada', 'empresa' => $empresa], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error en la base de datos', 'detalles' => $e->getMessage()], 500);
        }
    }




    public function update($id, Request $request){
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
            if($empresa->isEmpty()){
                return response()->json(['message' => 'Empresa no encontrada'], status: 404);
            }
            $user = User::find($empresa->usuario_id);
            if($user->isEmpty()){
                return response()->json(['message' => 'Usuario asociado a la empresa no encontrado'], status: 404);
            }
            $empresa->update($request->all());
            $empresa->save();
            $user->update($request->all());
            $user->save();
            return response()->json($empresa, status: 200);
        }catch(Exception $e){
            return response()->json(['message' => 'Error al actualizar la empresa: '. $e->getMessage()], status: 400);
        }
    }

    public function destroy($id){
        $empresa = Empresa::find($id);
        if($empresa == null){
            return response()->json(['message' => 'Empresa no encontrada'], status: 404);
        }
        $user = User::find($empresa->usuario_id );
        if($user == null){
            return response()->json(['message' => 'Usuario asociado a la empresa no encontrado'], status: 404);
        }
        $empresa->update(['isDeleted' => true]);
        $user->update(['isDeleted' => true]);

        $empresa->save();
        $user->save();
        return response()->json(['message' => 'Empresa y usuario eliminados correctamente'], status: 204);
    }
}
