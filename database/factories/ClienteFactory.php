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
            'dni' => fake()->unique()->randomNumber(8, true),
            'foto_dni' => 'https://example.com/images/test.jpg',
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
