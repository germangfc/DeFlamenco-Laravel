<?php

namespace App\Http\Controllers;

use App\Mail\PagoConfirmado;
use App\Services\VentaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class StripeController extends Controller
{
    protected $ventaService;

    public function __construct(VentaService $ventaService){
        $this->ventaService = $ventaService;
    }
    /**
     * Muestra la vista del checkout con el resumen del carrito.
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('cart.index', compact('cart'));
    }

    /**
     * Genera una sesión de Stripe Checkout a partir del carrito.
     */
    public function checkout()
    {
        // Obtener el carrito de la sesión
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->back()->with('error', 'El carrito está vacío.');
        }

        // Configura la API Key de Stripe
        Stripe::setApiKey(config('services.stripe.sk'));

        // Convertir los items del carrito en line items para Stripe.
        // Se asume que cada item del carrito tiene: idEvent, price y quantity.
        $lineItems = [];
        foreach ($cart as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    // Puedes adaptar el nombre a lo que necesites; aquí usamos el idEvent.
                    'product_data' => [
                        'name' => 'Evento: ' . $item['name'],
                    ],
                    // Stripe espera el precio en céntimos, por lo que se multiplica por 100.
                    'unit_amount' => $item['price'] * 100,
                ],
                'quantity' => $item['quantity'],
            ];
        }


        /* Debería hacerse justo después del pago, solo si tiene éxito */
        if (StripeSession::STATUS_COMPLETE){
            //llamar al generador de entrada que necesita un id de evento y un cliente que se buscara a partira del usuario, usuario el cual esta ahora mismo auth
            // Generar la lista de entradas que necesita el metodo generarTickets que se genera la lista de entradas en este metodo
            // y se disminuye la cantidad de entradas disponibles en el evento
            $user = auth()->user();
            $ticketList = $this->ventaService->generarTickets($cart, $user);
            if (!$ticketList) {
                return redirect()->route('eventos.index')->with('error', 'No se pudieron generar los tickets de la venta');
            }
            $venta = $this->ventaService->generarVenta($ticketList, $user);
            if (!$venta) {
                return redirect()->route('eventos.index')->with('error', 'No se pudo generar la venta');
            }

            // generar venta que necesita una lista de entradas en este metodo se genera la venta con maximo 5 lineas de venta
            // y se disminuye la cantidad de entradas disponibles en el evento


        }
        // Crear la sesión de Stripe Checkout
        $session = StripeSession::create([
            'line_items' => $lineItems,
            'mode' => 'payment',
            // Define la URL a la que Stripe redirigirá en caso de éxito
            'success_url' => route('stripe.success'),
            // Define la URL a la que Stripe redirigirá en caso de cancelar el pago
            'cancel_url'  => route('eventos'),
        ]);

        // Redirige al usuario a la sesión de Stripe Checkout
        return redirect()->away($session->url);
    }

    /**
     * Maneja el caso de pago exitoso.
     */
    public function success()
    {
        // Aquí puedes realizar acciones adicionales, por ejemplo:
        // - Registrar la venta en MongoDB.
        // - Crear los tickets correspondientes.
        // - Notificar al usuario (envío de email, etc.).

        // Enviar correo de confirmación
        Mail::to('yahyaelhadricgs@gmail.com')->send(new PagoConfirmado());

        // Limpiar el carrito de la sesión
        session()->forget('cart');

        // Redirigir a una ruta (por ejemplo, la lista de eventos) con mensaje de éxito
        return redirect()->route('eventos')->with('success', 'Pago confirmado');
    }
}
