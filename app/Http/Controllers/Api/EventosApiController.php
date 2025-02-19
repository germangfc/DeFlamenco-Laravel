<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
                'nombre' => 'required|string|max:255|unique:eventos',
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

    public function show($id)
    {
        $cacheKey = "evento_{$id}";

        $eventos = Cache::get($cacheKey);

        if (!$eventos) {
            $eventos = Evento::find($id);

            if (!$eventos) {
                return response()->json(['error' => 'Event not found'], 404);
            }

            Cache::put($cacheKey, $eventos, 20);
        }

        return response()->json($eventos, 200);
    }

    public function getByNombre($nombre)
    {
        $cacheKey = "eventos_nombre_";

        $eventos = Cache::get($cacheKey);

        if (!$eventos) {
            $eventos = Evento::where('nombre', 'like', '%' . $nombre . '%')->get();

            if ($eventos->isEmpty()) {
                return response()->json(['error' => 'Event not found'], 404);
            }

            Cache::put($cacheKey, $eventos, 20);
        }

        return response()->json($eventos, 200);
    }

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:255',
                'stock' => 'required|integer',
                'fecha' => 'required|date',
                'hora' => 'required|date_format:H:i:s',
                'direccion' => 'required|string|max:255',
                'ciudad' => 'required|string|max:255',
                'precio' => 'required|numeric',
            ]);

            $eventos = Evento::find($id);

            if ($eventos) {
                $eventos->update($validatedData);

                $cacheKey = "evento_{$id}";
                Cache::forget($cacheKey);

                return response()->json($eventos, 200);
            } else {
                return response()->json(['error' => 'Event not found'], 404);
            }
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        }
    }

    public function destroy($id)
    {
        $eventos = Evento::find($id);

        if ($eventos) {
            $eventos->delete();

            $cacheKey = "evento_{$id}";
            Cache::forget($cacheKey);

            return response()->json(['message' => 'Event deleted'], 200);
        } else {
            return response()->json(['error' => 'Event not found'], 404);
        }
    }
}
