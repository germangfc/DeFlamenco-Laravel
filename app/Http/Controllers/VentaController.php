<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Console\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Factory;
use Illuminate\View\View;

class VentaController extends Controller
{

    /**
     * Muestra el listado de ventas.
     *
     * @param Request $request para la peticion de busqueda.
     *
     * @return View con el listado de ventas.
     */
    public function index(Request $request)
    {
        // Consulta de ventas
        $ventas = Venta::orderBy('_id', 'DESC')->paginate(5);

        // Verifica la estructura de las ventas y las lineasVenta
        //dd($ventas);  // Detener la ejecución y ver el contenido de $ventas

        return view('ventas.index')->with('ventas', $ventas);
    }


    /**
     * Muestra el detalle de una venta.
     *
     * @param int $id
     *
     * @return Factory|Application|object|View a la vista de detalle de venta.
     */
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
