<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class VentaController extends Controller
{
    public function index(Request $request){
        $ventas = Venta::orderBy('created_at','DESC')->paginate(5);
        //$ventas = Venta::search($request->created_at)->orderBy('created_at','DESC')->paginate(5);
        return view('ventas.index')->with('ventas',$ventas);
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
