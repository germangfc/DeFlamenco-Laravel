<?php

namespace Database\Seeders;

use App\Models\Evento;
use Illuminate\Database\Seeder;

class EventosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Evento::create([
            'nombre' => 'Code40',
            'stock' => 100,
            'fecha' => '2025-02-14',
            'hora' => '15:41:34',
            'direccion' => 'Calle 123',
            'ciudad' => 'Bogotá',
            'precio' => 100.00,
        ]);

        Evento::create([
            'nombre' => 'NocheLeyendas',
            'stock' => 200,
            'fecha' => '2025-02-14',
            'hora' => '15:41:34',
            'direccion' => 'Calle 123',
            'ciudad' => 'Bogotá',
            'precio' => 200.00,
        ]);

        Evento::create([
            'nombre' => 'MadridFest',
            'stock' => 300,
            'fecha' => '2025-02-14',
            'hora' => '15:41:34',
            'direccion' => 'Calle 123',
            'ciudad' => 'Bogotá',
            'precio' => 300.00,
        ]);

        Evento::create([
            'nombre' => 'CocacolaFest',
            'stock' => 400,
            'fecha' => '2025-02-14',
            'hora' => '15:41:34',
            'direccion' => 'Calle 123',
            'ciudad' => 'Bogotá',
            'precio' => 400.00,
        ]);

        Evento::create([
            'nombre' => 'Rave',
            'stock' => 500,
            'fecha' => '2025-02-14',
            'hora' => '15:41:34',
            'direccion' => 'Calle 123',
            'ciudad' => 'Bogotá',
            'precio' => 500.00,
        ]);

        Evento::create([
            'nombre' => 'Churumbel',
            'stock' => 600,
            'fecha' => '2025-02-14',
            'hora' => '15:41:34',
            'direccion' => 'Calle 123',
            'ciudad' => 'Bogotá',
            'precio' => 600.00,
        ]);

        Evento::create([
            'nombre' => 'ElCigala',
            'stock' => 700,
            'fecha' => '2025-02-14',
            'hora' => '15:41:34',
            'direccion' => 'Calle 123',
            'ciudad' => 'Bogotá',
            'precio' => 700.00,
        ]);

        Evento::create([
            'nombre' => 'Paquirrin',
            'stock' => 800,
            'fecha' => '2025-02-14',
            'hora' => '15:41:34',
            'direccion' => 'Calle 123',
            'ciudad' => 'Bogotá',
            'precio' => 800.00,
        ]);

        Evento::create([
            'nombre' => 'TocToc',
            'stock' => 900,
            'fecha' => '2025-02-14',
            'hora' => '15:41:34',
            'direccion' => 'Calle 123',
            'ciudad' => 'Bogotá',
            'precio' => 900.00,
        ]);

        Evento::create([
            'nombre' => 'SevillaGrande',
            'stock' => 1000,
            'fecha' => '2025-02-14',
            'hora' => '15:41:34',
            'direccion' => 'Calle 123',
            'ciudad' => 'Bogotá',
            'precio' => 1000.00,
        ]);



    }
}
