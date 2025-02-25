<?php

namespace App\Mail;

use App\Models\Empresa;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmpresaBienvenida extends Mailable
{
    use Queueable, SerializesModels;

    public $empresa;
    public $usuario;

    public function __construct(Empresa $empresa, User $usuario)
    {
        $this->empresa = $empresa;
        $this->usuario = $usuario;
    }

    public function build(): EmpresaBienvenida
    {
        return $this->view('emails.empresa_bienvenida')
            ->with([
                'empresa' => $this->empresa,
                'usuario' => $this->usuario
            ]);
    }
}
