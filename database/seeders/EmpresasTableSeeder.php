<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmpresasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('empresas')->insert([
            [
                'cif' => 'B12345678',
                'nombre' => 'Empresa Ejemplo 1',
                'direccion' => 'Calle Falsa 123, Madrid',
                'imagen' => 'storage/empresa1.jpg',
                'telefono' => '+34 612345678',
                'email' => 'empresa1@example.com',
                'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012',
                'usuario_id' => 1,
                'lista_eventos' => json_encode([]),
                'isDeleted' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'cif' => 'B87654321',
                'nombre' => 'Empresa Ejemplo 2',
                'direccion' => 'Avenida Siempre Viva 742, Barcelona',
                'imagen' => 'storage/empresa2.jpg',
                'telefono' => '+34 698765432',
                'email' => 'empresa2@example.com',
                'cuentaBancaria' => 'ES98 7654 3210 9876 5432 1098',
                'usuario_id' => 2,
                'lista_eventos' => json_encode([]),
                'isDeleted' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
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
