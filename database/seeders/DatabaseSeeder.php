<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Models\Ticket;
use App\Models\Cliente;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Grpc\Call;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->count(50)->create();
        Cliente::factory()->count(50)->create();
        Ticket::factory(10)->create();
        $this->call([
            EmpresasTableSeeder::class
        ]);
    }
}
