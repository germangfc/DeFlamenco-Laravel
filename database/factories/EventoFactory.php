<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Evento>
 */
class EventoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->sentence(3),
            'stock' => $this->faker->numberBetween(0, 100),
            'fecha' => $this->faker->date(),
            'hora' => $this->faker->time(),
            'direccion' => $this->faker->streetAddress(),
            'ciudad' => $this->faker->city(),
            'precio' => $this->faker->randomFloat(2, 5, 500),
            'foto' => $this->faker->imageUrl(),
        ];
    }
}
