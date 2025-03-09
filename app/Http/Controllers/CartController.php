<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{

    /**
     * Muestra el contenido del carrito.
     *
     * @return View
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('cart.index', compact('cart'));
    }

    /**
     * Agregar un item al carrito.
     *
     * @param Request $request
     * @return RedirectResponse
     */
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

    /**
     * Eliminar un item del carrito.
     *
     * @param Request $request
     * @param int $idEvent
     * @return RedirectResponse
     */
    public function remove(Request $request, $idEvent)
    {
        $cart = session()->get('cart', []);

        $cart = array_filter($cart, function($item) use ($idEvent) {
            return $item['idEvent'] != $idEvent;
        });

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Item removido del carrito.');
    }

    /**
     * Actualizar la cantidad de un item en el carrito.
     *
     * @param Request $request
     * @param int $idEvent
     * @return RedirectResponse
     */
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
