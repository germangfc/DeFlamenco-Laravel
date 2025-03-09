<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsuariosClientesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuario = User::create([
            'name' => 'Juan Pérez',
            'email' => 'juan.perez@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'tipo' => 'cliente',
            'remember_token' => Str::random(10),
            'isDeleted' => false,
        ]);

        $cliente = Cliente::create([
            'user_id' => $usuario->id,
            'avatar' => 'https://images.ctfassets.net/h6goo9gw1hh6/2sNZtFAWOdP1lmQ33VwRN3/e40b6ea6361a1abe28f32e7910f63b66/1-intro-photo-final.jpg?w=1200&h=992&fl=progressive&q=70&fm=jpg',
            'is_deleted' => false
        ]);

        $usuario->assignRole('cliente');

        $usuario2 = User::create([
            'name' => 'María López',
            'email' => 'maria.lopez@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'tipo' => 'cliente',
            'remember_token' => Str::random(10),
            'isDeleted' => false,
        ]);

        Cliente::create([
            'user_id' => $usuario2->id,
            'avatar' => 'https://images.pexels.com/photos/27304892/pexels-photo-27304892.jpeg?cs=srgb&dl=pexels-shuvalova-natalia-415991090-27304892.jpg&fm=jpg',
            'is_deleted' => false
        ]);

        $usuario2->assignRole('cliente');
    }
}
