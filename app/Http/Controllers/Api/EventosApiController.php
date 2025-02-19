<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EventosApiController extends Controller
{
    public function index(){
        $eventos = Evento::all();
        return response()->json($eventos);
    }

    public function store(Request $request){
        try{
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:255|unique:eventos|min:3',
                'stock' => 'required|integer',
                'fecha' => 'required|date',
                'hora' => 'required|date_format:H:i:s',
                'direccion' => 'required|string|max:255',
                'ciudad' => 'required|string|max:255',
                'precio' => 'required|numeric',
            ]);

            $eventos = Evento::create($validatedData);
            return response()->json($eventos, 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        }
    }

    public function show($id){
        $eventos = Evento::find($id);
        if($eventos){
            return response()->json($eventos);
        }else{
            return response()->json(['error' => 'Evento no encontrado'], 404);
        }
    }

    public function getByNombre($nombre){
        $eventos = Evento::where('nombre', 'like', '%'.$nombre.'%')->get();
        if($eventos->isEmpty()){
            return response()->json(['error' => 'Evento no encontrado'], 404);
        } else {
            return response()->json($eventos);
        }
    }

    public function update(Request $request, $id){
        try{
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:255|min:3',
                'stock' => 'required|integer',
                'fecha' => 'required|date',
                'hora' => 'required|date_format:H:i:s',
                'direccion' => 'required|string|max:255',
                'ciudad' => 'required|string|max:255',
                'precio' => 'required|numeric',
            ]);

            $eventos = Evento::find($id);
            if($eventos){
                $eventos->update($validatedData);
                return response()->json($eventos, 200);
            }else{
                return response()->json(['error' => 'Evento no encontrado'], 404);
            }
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        }
    }

    public function destroy($id){
        $eventos = Evento::find($id);
        if($eventos){
            $eventos->delete();
            return response()->json(['message' => 'Evento eliminado'], 200);
        }else{
            return response()->json(['error' => 'Evento no encontrado'], 404);
        }
    }
}
