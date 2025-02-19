<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ActualizacionDatos extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;

    public function __construct(User $usuario)
    {
        $this->usuario = $usuario;
    }

    public function build()
    {
        return $this->view('emails.actualizacion_datos')
            ->with([
                'usuario' => $this->usuario,
            ])
            ->subject('Tus datos han sido actualizados correctamente en TABLAOPASS');
    }
}
