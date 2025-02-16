<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Datos de prueba de Tickets
        Ticket::insert([
            [
                'idEvent' => 'event123',
                'idClient' => 'client001',
                'price' => 50.00,
                'isReturned' => false,
            ],
            [
                'idEvent' => 'event456',
                'idClient' => 'client002',
                'price' => 75.00,
                'isReturned' => true,
            ],
            [
                'idEvent' => 'event789',
                'idClient' => 'client003',
                'price' => 100.00,
                'isReturned' => false,
            ],
        ]);
    }
}
