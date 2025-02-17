<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\Request;

class EventosController extends Controller
{

    public function index(Request $request)
    {
        $eventos = Evento::search($request->nombre)->OrderBy('id', 'ASC')->paginate(5);
        return view('eventos.index')->with('eventos', $eventos);
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
            'hora' => 'required|date_format:H:i:s',
            'direccion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:255',
            'precio' => 'required|numeric',
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
        $evento = Evento::find($id);
        return view('eventos.show', compact('evento'));
    }

    public function edit($id)
    {
        $eventos = Evento::find($id);
        return view('eventos.edit')->with('eventos', $eventos);
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
        ]);

        $evento = Evento::find($id);
        $evento->nombre = $request->nombre;
        $evento->stock = $request->stock;
        $evento->fecha = $request->fecha;
        $evento->hora = $request->hora;
        $evento->direccion = $request->direccion;
        $evento->ciudad = $request->ciudad;
        $evento->precio = $request->precio;
        $evento->save();

        return redirect()->route('eventos.index');
    }


    public function destroy($id)
    {
        try{
            $evento = Evento::find($id);
            $evento->delete();
            return redirect()->route('eventos.index');
        } catch (Exception $e) {
            return redirect()->route('eventos.index')->with('error', 'No se puede eliminar el evento');
        }
    }
}
