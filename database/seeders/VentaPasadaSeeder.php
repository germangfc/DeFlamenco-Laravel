<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Evento;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class VentaPasadaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'Vicente Maroto',
            'email' => 'vicente@example.com',
            'password' => Hash::make('12345678'),
            'tipo' => 'cliente',
            'remember_token' => null,
            'isDeleted' => false,
        ]);

        $user->assignRole('cliente');

        $cliente = Cliente::create([
            'user_id' => $user->id,
            'avatar' => 'https://album.mediaset.es/cimg/1000001/2017/02/14/v1.jpg',
            'is_deleted' => false,
        ]);

        $user2 = User::create([
            'name' => 'EC ENTERTAINMENT GROUP, S.L.',
            'email' => 'entretenimientogroup@empresa.com',
            'password' => Hash::make('12345678'),
            'tipo' => 'empresa',
            'remember_token' => null,
            'isDeleted' => false,
        ]);

        $user2->assignRole('empresa');

        // Crear empresa
        $empresa = Empresa::create([
            'cif' => 'B76289347',
            'name' => 'EC ENTERTAINMENT GROUP, S.L.',
            'direccion' => 'Calle Mallorca 18 , Tarragona',
            'imagen' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQsItl6yTwTmgSjHNQNntTXNbfdG4TScRnY-Q&s',
            'telefono' => '954123456',
            'email' => 'entretenimientogroup@empresa.com',
            'cuentaBancaria' => 'ES9121000418450200051332',
            'usuario_id' => $user2->id,
            'isDeleted' => false,
        ]);

        // Crear evento pasado
        $evento = Evento::create([
            'nombre' => 'Isabel Pantoja en Tarragona',
            'stock' => 100,
            'fecha' => '2024-11-30',
            'hora' => '21:00',
            'direccion' => 'Calle Larios',
            'ciudad' => 'Malaga',
            'precio' => 20.00,
            'foto' => 'https://ecientradasprocdn.azureedge.net/image_uploads/attachments/000/013/692/IsabelPantoja2_400x504.jpg',
            'descripcion' => 'La Gira «50 Años» Isabel Pantoja es un evento musical que celebra la prolífica y exitosa carrera de la reconocida cantante española.',
            'empresa_id' => $empresa->id,
        ]);

        // Crear ticket asociado al evento pasado
        $ticket = Ticket::create([
            'idEvent' => $evento->id,
            'idClient' => $cliente->id,
            'price' => $evento->precio,
            'isReturned' => false,
        ]);

        // Crear venta con el ticket del evento pasado
        $venta = Venta::create([
            'guid' => Str::uuid(),
            'lineasVenta' => [
                [
                    $ticket->id,
                    $ticket->price,
                    $evento->nombre,
                    $evento->fecha,
                    $evento->hora,
                    $evento->ciudad
                ]
            ]
        ]);
    }
}
