<?php

namespace Database\Factories;

use App\Models\Cliente;
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
            'user_id' => self::$userId++,
            'dni' => $this->faker->unique()->numerify('##########'),
            'foto_dni' => $this->faker->imageUrl(200, 200, 'people'),
            'lista_entradas' => [$this->faker->word(), $this->faker->word()],
            'is_deleted' => false
        ];
    }
}
