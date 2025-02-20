<?php

namespace App\Mail;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClienteBienvenido extends Mailable
{
    use Queueable, SerializesModels;

    public $cliente;
    public $usuario;

    public function __construct(Cliente $cliente, User $usuario)
    {
        $this->cliente = $cliente;
        $this->usuario = $usuario;
    }

    public function build(): ClienteBienvenido
    {
        return $this->view('emails.cliente_bienvenido')
            ->with([
                'cliente' => $this->cliente,
                'usuario' => $this->usuario
            ]);
    }
}
