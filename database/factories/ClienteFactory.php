<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cliente>
 */
class ClienteFactory extends Factory
{
    protected $model = Cliente::class;
    protected static $userId = 1;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'avatar' => 'https://i.pravatar.cc/300',
            'is_deleted' => false
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Cliente $cliente) {
            $user = User::find($cliente->user_id);
            $user->assignRole('cliente');
        });
    }
}
