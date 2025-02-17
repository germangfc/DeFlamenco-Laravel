<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaControllerApi extends Controller
{
    public function getAll(Request $request){
        $query = Empresa::query();
        if($request->has('nombre')){
            $query->where('nombre', 'LIKE', '%'. $request->nombre. '%');
        }
        $empresas = $query->paginate(5);
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

    public function create(Request $request){
        $request->validate([
            'cif'=> 'required|regex:/^[A-HJNP-SUVW][0-9]{7}[0-9A-J]$/',
            'nombre'=> 'required|max:255',
            'direccion'=> 'required|max:255',
            'cuentaBancaria'=>'required|regex:/^ES\d{2}\s?\d{4}\s?\d{4}\s?\d{2}\s?\d{10}$/',
            'telefono'=> 'required|regex:/^(\+34|0034)?[679]\d{8}$/',
            'correo'=> 'required|email|max:255'
        ]);
    }

    public function update($id, Request $request){
    }

    public function delete($id){
    }
}
