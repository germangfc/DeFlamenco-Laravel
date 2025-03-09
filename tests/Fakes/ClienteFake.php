<?php
namespace Tests\Fakes;

class ClienteFake
{
    public $id;
    public $user_id;

    public function __construct($attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->$key = $value;
        }
    }

    // Devuelve una colección vacía si el user_id es -1 (o equivalente), simulando que no se encontró el cliente.
    public static function FindByUserId($userId)
    {
        if ($userId == -1) {   // Usamos '==' para comparar sin conflicto de tipos.
            return collect([]);
        }
        return collect([ new self([
            'id'      => 1,
            'user_id' => $userId,
        ]) ]);
    }
}
