<?php
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Empresa;
class EmpresaFactory extends Factory
{

    protected $model = Empresa::class;
    protected static $empresaId = 1;

    public function definition()
    {
        return [
            'cif' => $this->generateValidCIF(),
            'nombre' => $this->faker->company,
            'direccion' => $this->faker->address,
            'imagen' => $this->faker->imageUrl(200, 200, 'business'),
            'telefono' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'cuentaBancaria' => $this->faker->iban('ES'),
            'usuario_id' => User::factory(),
            'lista_eventos' => json_encode($this->faker->words(3)),
            'isDeleted' => false,
        ];
    }

    private function generateValidCIF()
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
