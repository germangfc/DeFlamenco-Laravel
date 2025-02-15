<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;

class StripeController extends Controller
{
    public  function index(){
        return view('main');
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
            'cancel_url' => route('main'),
        ]);
        return redirect()->away($session->url);
    }

    public function success(){
        return view('main');
    }
}

