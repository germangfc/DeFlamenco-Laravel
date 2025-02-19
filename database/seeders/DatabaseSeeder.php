<?php

namespace Database\Seeders;

use App\Models\Empresa;
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
        // Vaciar las colecciones antes de crear nuevos datos ( en Mongo no aplica el migrate:fresh )
        Ticket::truncate();  // Vacía la colección de Ticket
        Venta::truncate();   // Vacía la colección de Venta

        Ticket::factory(10)->create();
        Venta::factory()->count(5)->create();

        $this->call([
            UserSeeder::class
        ]);
        Cliente::factory()->count(25)->create();
        Empresa::factory()->count(25)->create();
    }
}
