<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Evento;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EventosController extends Controller
{
    public function getAll(Request $request)
    {
        $eventos = Evento::search($request->nombre)->orderBy('id', 'ASC')->paginate(4);

        return view('card', compact('eventos'));
    }

    public function create()
    {
        return view('eventos.store');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:eventos',
            'stock' => 'required|integer',
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i:s',
            'direccion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:255',
            'precio' => 'required|numeric',
            'foto' =>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        try{
            $eventos= Evento::create($request->all());
            return redirect()->route('eventos.index');
        } catch (Exception $e) {
            return redirect()->route('eventos.create')->with('error', 'El evento ya existe');
        }
    }

    public function show($id)
    {
        $cacheKey = "evento_{$id}";

        $evento = Cache::get($cacheKey);

        if (!$evento) {
            $evento = Evento::find($id);

            if (!$evento) {
                return response()->json(['message' => 'Evento no encontrado'], 404);
            }

            Cache::put($cacheKey, $evento, 60);
        }

        return view('eventos.show', compact('evento'));
    }


    public function edit($id)
    {
        $cacheKey = "evento_{$id}";

        $evento = Cache::get($cacheKey);

        if (!$evento) {
            $evento = Evento::find($id);

            if (!$evento) {
                return response()->json(['message' => 'Evento no encontrado'], 404);
            }

            Cache::put($cacheKey, $evento, 60);
        }

        return view('eventos.edit', compact('evento'));
    }



    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'stock' => 'required|integer',
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i:s',
            'direccion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:255',
            'precio' => 'required|numeric',
            'foto' =>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $evento = Evento::find($id);

        if (!$evento) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        $evento->nombre = $request->nombre;
        $evento->stock = $request->stock;
        $evento->fecha = $request->fecha;
        $evento->hora = $request->hora;
        $evento->direccion = $request->direccion;
        $evento->ciudad = $request->ciudad;
        $evento->precio = $request->precio;
        $evento->foto = $request->foto;
        $evento->save();

        $cacheKey = "evento_{$id}";
        Cache::forget($cacheKey);

        Cache::put($cacheKey, $evento, 60);

        return redirect()->route('eventos.index');
    }



    public function destroy($id)
    {
        try {
            $evento = Evento::find($id);

            if (!$evento) {
                return redirect()->route('eventos.index')->with('error', 'Evento no encontrado');
            }

            $evento->delete();

            $cacheKey = "evento_{$id}";
            Cache::forget($cacheKey);

            return redirect()->route('eventos.index');
        } catch (Exception $e) {
            return redirect()->route('eventos.index')->with('error', 'No se puede eliminar el evento');
        }
    }

}
