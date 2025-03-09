<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Evento;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class EventosController extends Controller
{

    /**
     *  Devuelve la vista con el listado de eventos y paginado.
     *
     * @param Request $request para la peticion de busqueda.
     *
     * @return View
     */
    public function getAll(Request $request)
    {
        $eventos = Evento::search($request->only([
            'query', 'fecha', 'precio_min', 'precio_max'
        ]))
            ->where('fecha', '>', now()->format('Y-m-d'))
            ->orderBy('id', 'ASC')
            ->paginate(12);

        return view('eventos.index', compact('eventos'));
    }


    /**
     *  Devuelve la vista con el detalle de un evento solo para admins.
     *
     * @param Request $request para la peticion de busqueda.
     *
     * @return View con el detalle del evento.
     */

    public function index(Request $request)
    {
        $eventos = Evento::search($request->only([
            'query', 'fecha', 'precio_min', 'precio_max'
        ]))
            ->orderBy('id', 'ASC')
            ->paginate(12);

        return view('eventos.index-admin', compact('eventos'));
    }


    /**
     *  Devuelve la vista con el detalle de un evento solo para empresas.
     *
     * @return View con el detalle del evento.
     */
    public function showMeEvents()
    {
        $empresa = Empresa::where('usuario_id', auth()->id())->firstOrFail();

        $eventos = $empresa->eventos()->orderBy('fecha', 'desc')->get();

        return view('eventos.index-me', compact('eventos', 'empresa'));
    }


    /**
     *  Devuelve la vista con el detalle de un evento.
     *
     * @return View con el detalle del evento.
     */
    public function create()
    {
        return view('eventos.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:100|unique:eventos|min:3',
                'descripcion' => 'required|string|min:5|max:500',
                'stock' => 'required|integer',
                'fecha' => 'required|date|after:today',
                'hora' => 'required',
                'direccion' => 'required|string|max:255',
                'ciudad' => 'required|string|max:255',
                'precio' => 'required|numeric',
                'foto' => 'required|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            $fotoPath = "";
            if ($request->hasFile('foto')) {
                $image = $request->file('foto');
                $timestamp = now()->timestamp;
                $customName = 'evento_' . $request->nombre . "_" . $timestamp . '.' . $image->getClientOriginalExtension();
                $image->storeAs('images', $customName, 'public');
                $fotoPath = $customName;
            }

            $empresa = Empresa::where('usuario_id', auth()->user()->id)->first();

            $evento = Evento::create([
                'nombre' => $request->nombre,
                'stock' => $request->stock,
                'fecha' => $request->fecha,
                'hora' => $request->hora,
                'direccion' => $request->direccion,
                'ciudad' => $request->ciudad,
                'precio' => $request->precio,
                'descripcion'=>$request->descripcion,
                'foto' => $fotoPath,
                'empresa_id' => $empresa->id
            ]);

            $cacheKey = "evento_{$evento->id}";
            Cache::put($cacheKey, $evento, 60);

            return redirect()->route('eventos')
                ->with('success', '¡Evento creado exitosamente!');
        } catch (ValidationException $e) {
            return redirect()->route('eventos.create')
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            return redirect()->route('eventos.create')
                ->with('error', 'Hubo un problema al crear el evento');
        }
    }

    /**
     *  Devuelve la vista con el detalle de un evento.
     *
     * @param int $id del evento a mostrar.
     *
     * @return Factory|\Illuminate\Contracts\View\View|Application|object con el detalle del evento.
     */

    public function show($id)
    {
        // Se obtiene el evento solicitado directamente de la base de datos
        $cacheKey = "evento_{$id}";

        $evento = Cache::remember($cacheKey, 60, function () use ($id) {
            return Evento::find($id);
        });

        if (!$evento) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        // Se obtienen el evento anterior y siguiente según el id
        $eventoAnterior = Evento::where('id', '<', $evento->id)
            ->orderBy('id', 'desc')
            ->first();
        $eventoSiguiente = Evento::where('id', '>', $evento->id)
            ->orderBy('id', 'asc')
            ->first();

        // Se obtiene el listado completo de eventos para el slider
        $eventos = Evento::paginate(12);

        return view('eventos.show', compact('evento', 'eventoAnterior', 'eventoSiguiente', 'eventos'));
    }



    /**
     *  Devuelve la vista con el detalle de un evento.
     *
     * @param int $id del evento a mostrar.
     *
     * @return Factory|View|Application|object con el detalle del evento.
     */
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



    /**
     *  Actualiza un evento.
     *
     * @param Request $request con los datos del evento a actualizar.
     *
     * @param int $id del evento a actualizar.
     *
     * @return RedirectResponse con el resultado de la actualización.
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255|unique:eventos,nombre,' . $id . '|min:3',
                'stock' => 'required|integer|min:1',
                'fecha' => 'required|date|after:today',
                'hora' => 'required',
                'direccion' => 'required|string|max:255',
                'ciudad' => 'required|string|max:255',
                'precio' => 'required|numeric|min:0',
                'descripcion' => 'required|string|min:5|max:500',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            $evento = Evento::find($id);

            if (!$evento) {
                return response()->json(['message' => 'Evento no encontrado'], 404);
            }

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
                'descripcion'=>$request->descripcion,
                'foto' => $fotoPath,
            ]);

            $cacheKey = "evento_{$id}";
            Cache::forget($cacheKey);
            Cache::put($cacheKey, $evento, 60);

            return redirect()->route('eventos.show', $id)
                ->with('success', '¡Evento actualizado exitosamente!');
        } catch (ValidationException $e) {
            return redirect()->route('eventos.edit', $id)
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            return redirect()->route('eventos.edit', $id)
                ->with('error', 'Hubo un problema al actualizar el evento');
        }
    }


    /**
     *  Elimina un evento.
     *
     * @param int $id del evento a eliminar.
     *
     * @return RedirectResponse con el resultado de la eliminación.
     */
    public function destroy($id)
    {
        try {
            $evento = Evento::find($id);

            if (!$evento) {
                return redirect()->route('eventos')->with('error', 'Evento no encontrado');
            }

            $evento->delete();

            $cacheKey = "evento_{$id}";
            Cache::forget($cacheKey);

            return redirect()->route('eventos')->with('success', '¡Evento eliminado exitosamente!');
        } catch (Exception $e) {
            return redirect()->route('eventos')->with('error', 'No se puede eliminar el evento');
        }
    }

}
