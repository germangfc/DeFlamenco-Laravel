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
        $nombre = $this->faker->sentence(3);
        return [
            'nombre' => $nombre,
            'stock' => $this->faker->numberBetween(0, 100),
            'fecha' => $this->faker->dateTimeBetween('now', '+2 year')->format('Y-m-d'),
            'hora' => $this->faker->dateTime()->format('H:i'),
            'direccion' => 'IES Luis Vives',
            'ciudad' => 'Leganes',
            'precio' => $this->faker->randomFloat(2, 5, 500),
            'foto' => "https://granadateguia.com/wp-content/uploads/2023/02/baile-flamenco-870x480.jpg",
            'descripcion' => "El mayor evento de música flamenca de 2025 en España 🔥
                        Un line up inmejorable con los mejores artistas y djs de la escena repartidos en 4 áreas musicales 🎵
                        El ambientazo que solo encuentras con {$nombre}  con una fiesta non stop de más de 6 horas 🕺🏼
                        Todas las fiestas de {$nombre} en exclusiva en De Flamenco 🎁",
        ];
    }
}
