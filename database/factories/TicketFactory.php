<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'idEvent' => $this->faker->numberBetween(1, 100),
            'idClient' => $this->faker->numberBetween(1, 100),
            'price' => $this->faker->randomFloat(2, 0, 1000),
            'isReturned' => $this->faker->boolean(),
        ];
    }
}
