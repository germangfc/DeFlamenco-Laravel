<?php

namespace App\Http\Controllers;

use App\Mail\PagoConfirmado;
use App\Services\VentaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class StripeController extends Controller
{
    protected $ventaService;

    public function __construct(VentaService $ventaService){
        $this->ventaService = $ventaService;
    }

    /**
     * Muestra el carrito de la compra.
     * @return Factory|View|Application|object Vista del carrito de la compra.
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('cart.index', compact('cart'));
    }

    /**
     * Agregar un item al carrito.
     * @return RedirectResponse Redirecciona al carrito de la compra.
     */
    public function checkout()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->back()->with('error', 'El carrito está vacío.');
        }

        Stripe::setApiKey(config('services.stripe.sk'));

        $lineItems = [];
        foreach ($cart as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Evento: ' . $item['name'],
                    ],
                    'unit_amount' => $item['price'] * 100,
                ],
                'quantity' => $item['quantity'],
            ];
        }

        // Crear sesión de pago en Stripe
        $session = StripeSession::create([
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('stripe.success'),  // Aquí se procesará la venta después del pago
            'cancel_url'  => route('eventos'),
        ]);

        return redirect()->away($session->url);
    }

    /**
     * Procesa la venta después de un pago exitoso.
     *
     *
     * @return RedirectResponse Redirecciona a la página de eventos.
     */

    public function success()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('eventos')->with('error', 'No hay productos en el carrito.');
        }

        $user = auth()->user();

        // Generar tickets (esto va a usar las líneas de venta que has mencionado)
        $ticketList = $this->ventaService->generarTickets($cart, $user);
        if (!$ticketList) {
            return redirect()->route('eventos')->with('error', 'No se pudieron generar los tickets.');
        }

        // Generar venta
        $venta = $this->ventaService->generarVenta($ticketList, $user);
        if (!$venta) {
            return redirect()->route('eventos')->with('error', 'No se pudo registrar la venta.');
        }

        // Enviar correo con los tickets generados
        Mail::to($user->email)->send(new PagoConfirmado($venta));

        // Limpiar carrito
        session()->forget('cart');

        return redirect()->route('eventos')->with('success', 'Pago confirmado y venta registrada.');
    }




}
