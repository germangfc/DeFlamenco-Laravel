<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EliminacionCuenta extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;

    public function __construct(User $usuario)
    {
        $this->usuario = $usuario;
    }

    public function build()
    {
        return $this->view('emails.eliminacion_cuenta')
            ->with([
                'usuario' => $this->usuario,
            ])
            ->subject('Confirmación de baja en TABLAOPASS');
    }
}
