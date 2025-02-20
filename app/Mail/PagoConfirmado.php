<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PagoConfirmado extends Mailable
{
    use Queueable, SerializesModels;

    public function build()
    {
        return $this->subject('Pago confirmado')
            ->view('emails.pago');
    }
}
