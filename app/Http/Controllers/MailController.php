<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function sendEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'greeting' => 'required|string',
            'messageContent' => 'required|string',
        ]);

        Mail::send('emails.template', [
            'subject' => $validated['subject'],
            'greeting' => $validated['greeting'],
            'messageContent' => $validated['messageContent'],
        ], function ($message) use ($validated) {
            $message->to($validated['email'])
                ->subject($validated['subject']);
        });

        return response()->json(['message' => 'Correo enviado correctamente.']);
    }
}
