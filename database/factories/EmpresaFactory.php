<?php

namespace Database\Factories;

use App\Models\Empresa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Random\RandomException;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Empresa>
 */
class EmpresaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cif' => $this->generateValidCIF(),
            'nombre' => $this->faker->company,
            'direccion' => $this->faker->address,
            'imagen' => $this->faker->imageUrl(200, 200, 'business'),
            'telefono' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'cuentaBancaria' => $this->faker->iban('ES'),
            'usuario_id' => User::factory()->create(['tipo' => 'empresa'])->id,
            'lista_eventos' => json_encode($this->faker->words(3)),
            'isDeleted' => false,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Empresa $empresa) {
            $user = User::find($empresa->usuario_id);
            $user->assignRole('empresa');
        });
    }

    /**
     * @throws RandomException
     */
    private function generateValidCIF(): string
    {
        $letrasValidas = 'ABCDEFGHJKLMNPQRSUVW';
        $primerCaracter = $letrasValidas[random_int(0, strlen($letrasValidas) - 1)];
        $numeros = str_pad(random_int(0, 9999999), 7, '0', STR_PAD_LEFT);
        $ultimoCaracter = random_int(0, 9);
        $letrasFinales = 'ABCDEFGHIJ';

        if (random_int(0, 1)) {
            $ultimoCaracter = $letrasFinales[random_int(0, strlen($letrasFinales) - 1)];
        }

        return $primerCaracter . $numeros . $ultimoCaracter;
    }
}
