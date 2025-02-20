<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Models\Evento;
use App\Models\Ticket;
use App\Models\Cliente;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Venta;
use Grpc\Call;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Ticket::factory(10)->create();

        $this->call([
            UserSeeder::class,
        ]);
        Cliente::factory()->count(25)->create();
        Empresa::factory()->count(25)->create();
        Venta::factory()->count(5)->create();
        Evento::factory()->count(50)->create();
    }
}
