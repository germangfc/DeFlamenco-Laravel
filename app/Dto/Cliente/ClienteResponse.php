<?php

namespace App\Dto\Cliente;

class ClienteResponse
{

    public string $nombre;
    public string $email;
    public string $dni;
    public ?string $foto_dni;


    public function __construct($user,$cliente)
    {
        $this->nombre = $user->name;
        $this->email = $user->email;
        $this->dni = $cliente->dni;
        $this->foto_dni = $cliente->foto_dni;
    }

    public function toArray()
    {
        return [
            'name'=> $this->nombre,
            'email'=>$this->email,
            'dni' => $this->dni,
            'foto_dni' => $this->foto_dni
        ];
    }

}
