<?php

namespace Tests\Unit\Services;

use App\Models\Empresa;
use Database\Factories\EmpresaFactory;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use App\Services\VentaService;
use App\Models\User;
use App\Models\Venta;
use App\Models\Evento;
use App\Models\Cliente;
use App\Models\Ticket;
use Illuminate\Support\Facades\Log;

class VentaServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $ventaService;

    public function setUp(): void
    {
        parent::setUp();
        $this->ventaService = new VentaService();
    }

    public function test_it_generates_a_sale_successfully()
    {
        $this->seed(UserSeeder::class);

        // Crear la empresa
        $empresa = Empresa::factory()->create();

        // Crear el evento asociado a la empresa
        $evento = Evento::create([
            'nombre' => 'Concierto de prueba',
            'fecha' => now(),
            'hora' => now()->addHours(2),
            'ciudad' => 'Madrid',
            'precio' => 50,
            'direccion' => 'calle de al lao',
            'stock' => 100,
            'empresa_id' => $empresa->id
        ]);

        // Crear un usuario de prueba y asignarle el rol 'empresa'
        $user = User::factory()->create();
        $user->assignRole('empresa'); // Asegúrate de asignar el rol

        // Crear una lista de tickets
        $ticketList = [
            (object)[
                '_id' => 1,
                'idEvent' => $evento->id,
                'price' => $evento->precio
            ]
        ];

        // Generar la venta
        $venta = $this->ventaService->generarVenta($ticketList, $user);

        $this->assertNotNull($venta);
        $this->assertEquals(1, count($venta->lineasVenta));
        $this->assertEquals($evento->nombre, $venta->lineasVenta[0][2]);

        // Verificar que el stock se haya reducido
        $evento->refresh();
        $this->assertEquals(99, $evento->stock);
    }



    public function test_it_returns_null_when_event_does_not_exist()
    {
        $this->seed(UserSeeder::class);
        // Crear un usuario de prueba
        $user = User::factory()->create();

        // Crear una lista de tickets con un evento inexistente
        $ticketList = [
            (object)[
                '_id' => 1,
                'idEvent' => 99999, // Evento inexistente
                'price' => 50
            ]
        ];

        $venta = $this->ventaService->generarVenta($ticketList, $user);

        $this->assertNull($venta);
    }

    public function test_it_generates_tickets_from_cart_successfully()
    {
        $this->seed(UserSeeder::class);

        // Crear un evento de prueba
        $empresa = Empresa::factory()->create();
        $evento = Evento::create([
            'nombre' => 'Concierto de prueba',
            'fecha' => now(),
            'hora' => now()->addHours(2),
            'ciudad' => 'Madrid',
            'precio' => 50,
            'direccion' => 'calle de al lao',
            'stock' => 100,
            'empresa_id' => $empresa->id
        ]);

        // Crear un usuario de prueba
        $user = User::factory()->create();

        // Crear un cliente asociado al usuario
        $cliente = Cliente::factory()->create(['user_id' => $user->id]);

        // Crear un carrito de compra
        $cart = [
            ['idEvent' => $evento->id, 'quantity' => 2]
        ];

        // Generar los tickets
        $ticketList = $this->ventaService->generarTickets($cart, $user);

        $this->assertCount(2, $ticketList);
        $this->assertEquals($evento->precio, $ticketList[0]->price);
    }

    public function test_it_returns_empty_array_when_ticket_generation_fails()
    {
        $this->seed(UserSeeder::class);
        $user = User::factory()->create();
        $cart = []; // Carrito vacío

        $ticketList = $this->ventaService->generarTickets($cart, $user);

        $this->assertIsArray($ticketList);
        $this->assertCount(0, $ticketList);
    }


    public function test_it_returns_null_when_ticket_cannot_be_generated()
    {
        $this->seed(UserSeeder::class);
        // Crear un evento inexistente
        $eventoId = 99999;

        // Crear un usuario de prueba
        $user = User::factory()->create();

        // Intentar generar un ticket para un evento que no existe
        $ticket = $this->ventaService->generarTicket($eventoId, $user);

        $this->assertNull($ticket);
    }

    public function test_it_returns_null_when_client_not_found_for_ticket()
    {
        $this->seed(UserSeeder::class);
        $empresa = Empresa::factory()->create();
        $evento = Evento::create([
            'nombre' => 'Concierto de prueba',
            'fecha' => now(),
            'hora' => now()->addHours(2),
            'ciudad' => 'Madrid',
            'precio' => 50,
            'direccion' => 'calle de al lao',
            'stock' => 100,
            'empresa_id' => $empresa->id
        ]);

        // Crear un usuario de prueba pero no un cliente asociado
        $user = User::factory()->create();

        // Intentar generar un ticket
        $ticket = $this->ventaService->generarTicket($evento->id, $user);

        $this->assertNull($ticket);
    }
}
