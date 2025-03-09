<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        // Consulta de ventas
        $ventas = Venta::orderBy('_id', 'DESC')->paginate(5);

        // Verifica la estructura de las ventas y las lineasVenta
        //dd($ventas);  // Detener la ejecución y ver el contenido de $ventas

        return view('ventas.index')->with('ventas', $ventas);
    }


    public function show($id){
        $cacheKey = "venta_{$id}";
        $venta = Cache::get($cacheKey);

        if (!$venta) {
            $venta = Venta::find($id);

            if(!$venta) { return redirect()->route('ventas.index')->with('error','Venta no encontrada'); }

            Cache::put($cacheKey,$venta, 60);
        }

        return view('ventas.show', compact('venta'));

    }
}
