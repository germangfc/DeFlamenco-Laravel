<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller
{
    // Mostrar el contenido del carrito
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('cart.index', compact('cart'));
    }

    // Agregar un item al carrito
    public function add(Request $request)
    {
        // Validar datos
        $data = $request->validate([
            'idEvent'  => 'required',
            'price'    => 'required|numeric',
            'quantity' => 'sometimes|numeric|min:1|max:5',
            'name'     => 'required'
        ]);

        $data['quantity'] = $data['quantity'] ?? 1;

        // Obtener carrito de la sesión o iniciarlo vacío
        $cart = session()->get('cart', []);

        // Opcional: Verificar si el item ya existe para actualizar cantidad
        $found = false;
        foreach ($cart as &$item) {
            if ($item['idEvent'] == $data['idEvent']) {
                $item['quantity'] += $data['quantity'];
                $found = true;
                break;
            }
        }
        if (!$found) {
            $cart[] = $data;
        }

        // Guardar el carrito en la sesión
        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Item agregado al carrito.');
    }

    // Remover un item del carrito
    public function remove(Request $request, $idEvent)
    {
        $cart = session()->get('cart', []);

        $cart = array_filter($cart, function($item) use ($idEvent) {
            return $item['idEvent'] != $idEvent;
        });

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Item removido del carrito.');
    }

    // Actualizar la cantidad de un item
    public function update(Request $request, $idEvent)
    {
        $data = $request->validate([
            'quantity' => 'required|numeric|min:1'
        ]);

        $cart = session()->get('cart', []);
        foreach ($cart as &$item) {
            if ($item['idEvent'] == $idEvent) {
                $item['quantity'] = $data['quantity'];
                break;
            }
        }
        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Carrito actualizado.');
    }
}
