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
        $eventos = Evento::search($request->only([
            'query', 'fecha', 'precio_min', 'precio_max'
        ]))
            ->orderBy('id', 'ASC')
            ->paginate(12);

        return view('eventos.index', compact('eventos'));
    }

    public function index(Request $request)
    {
        $eventos = Evento::search($request->only([
            'query', 'fecha', 'precio_min', 'precio_max'
        ]))
            ->orderBy('id', 'ASC')
            ->paginate(12);

        return view('eventos.index-admin', compact('eventos'));
    }



    public function create()
    {
        return view('eventos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:eventos',
            'stock' => 'required|integer',
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i',
            'direccion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:255',
            'precio' => 'required|numeric',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        try {
            $fotoPath = "";
            if ($request->hasFile('foto')) {
                $image = $request->file('foto');

                $timestamp = now()->timestamp;
                $customName = 'evento_' . $request->nombre . "_" . $timestamp. '.' . $image->getClientOriginalExtension();

                $image->storeAs('images', $customName, 'public');
                $fotoPath=$customName;
            }

            $evento = Evento::create([
                'nombre' => $request->nombre,
                'stock' => $request->stock,
                'fecha' => $request->fecha,
                'hora' => $request->hora,
                'direccion' => $request->direccion,
                'ciudad' => $request->ciudad,
                'precio' => $request->precio,
                'foto' => $fotoPath,
            ]);

            return redirect()->route('eventos')
                ->with('success', '¡Evento creado exitosamente!');
        } catch (Exception $e) {
            return redirect()->route('eventos.create')
                ->with('error', 'Hubo un problema al crear el evento');
        }
    }


    public function show($id)
    {
        $cacheKey = "evento_{$id}";

        $evento = Cache::remember($cacheKey, 60, function () use ($id) {
            return Evento::find($id);
        });

        if (!$evento) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        $eventoAnterior = Evento::where('id', '<', $evento->id)->orderBy('id', 'desc')->first();
        $eventoSiguiente = Evento::where('id', '>', $evento->id)->orderBy('id', 'asc')->first();

        return view('eventos.show', compact('evento', 'eventoAnterior', 'eventoSiguiente'));
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
            'nombre' => 'required|string|max:255|unique:eventos,nombre,' . $id,
            'stock' => 'required|integer',
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i',
            'direccion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:255',
            'precio' => 'required|numeric',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $evento = Evento::find($id);

        if (!$evento) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        try {
            $fotoPath = $evento->foto;
            if ($request->hasFile('foto')) {
                $image = $request->file('foto');

                $timestamp = now()->timestamp;
                $customName = 'evento_' . $request->nombre . "_" . $timestamp . '.' . $image->getClientOriginalExtension();

                $image->storeAs('images', $customName, 'public');
                $fotoPath = $customName;
            }

            $evento->update([
                'nombre' => $request->nombre,
                'stock' => $request->stock,
                'fecha' => $request->fecha,
                'hora' => $request->hora,
                'direccion' => $request->direccion,
                'ciudad' => $request->ciudad,
                'precio' => $request->precio,
                'foto' => $fotoPath,
            ]);

            $cacheKey = "evento_{$id}";
            Cache::forget($cacheKey);
            Cache::put($cacheKey, $evento, 60);

            return redirect()->route('eventos.show', $id)
                ->with('success', '¡Evento actualizado exitosamente!');
        } catch (Exception $e) {
            return redirect()->route('eventos.edit', $id)
                ->with('error', 'Hubo un problema al actualizar el evento');
        }
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
