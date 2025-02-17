<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmpresasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('es_ES');

        foreach (range(1, 10) as $index) {
            DB::table('empresas')->insert([
                'cif' => $this->generarCIF(),
                'nombre' => $faker->company,
                'direccion' => $faker->address,
                'imagen' => 'storage/empresa' . $index . '.jpg',
                'telefono' => $faker->numerify('+34 6########'),
                'email' => $faker->unique()->safeEmail,
                'cuenta' => 'ES' . $faker->numerify('## #### #### ## ########'),
                'usuario_id' => rand(1, 10), // Ajusta según los IDs de usuarios disponibles
                'evento_id' => rand(1, 5), // Ajusta según los IDs de eventos disponibles
                'isdeleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Genera un CIF válido para España
     */
    private function generarCIF(): string
    {
        $letraInicial = 'ABCDEFGHJNPQRSUVW';
        $numeros = str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT);
        $letraFinal = '0123456789ABCDEFGHIJ'[rand(0, 9)]; // Simplificación del cálculo del dígito de control

        return $letraInicial[rand(0, strlen($letraInicial) - 1)] . $numeros . $letraFinal;
    }
}
