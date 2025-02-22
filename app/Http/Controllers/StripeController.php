<?php

namespace App\Http\Controllers;

use App\Mail\PagoConfirmado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;

class StripeController extends Controller
{
    public  function index(){
        return redirect()->route('eventos');
    }

    public function checkout(){
        Stripe::setApiKey(config('services.stripe.sk'));

        $session = \Stripe\Checkout\Session::create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'T-shirt',
                    ],
                    'unit_amount' => 50,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('success'),
            'cancel_url' => route('eventos'),
        ]);
        return redirect()->away($session->url);
    }

    public function success(){
        Mail::to('yahyaelhadricgs@gmail.com')->send(new PagoConfirmado());

        return redirect()->route('eventos');
    }
}

