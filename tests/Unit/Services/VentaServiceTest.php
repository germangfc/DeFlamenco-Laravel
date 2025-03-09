<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\VentaService;
use App\Models\User;
use App\Models\Venta;
use App\Models\Evento;
use App\Models\Cliente;
use App\Models\Ticket;
use Tests\Fakes\VentaFake;
use Tests\Fakes\EventoFake;
use Tests\Fakes\ClienteFake;
use Tests\Fakes\TicketFake;
use Illuminate\Support\Facades\Log;

class VentaServiceTest extends TestCase
{
    protected VentaService $ventaService;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Redireccionamos los modelos reales a sus versiones fake para evitar tocar la base de datos de producción.
        if (!defined('VENTA_ALIAS_SET')) {
            class_alias(VentaFake::class, Venta::class);
            define('VENTA_ALIAS_SET', true);
        }
        if (!defined('EVENTO_ALIAS_SET')) {
            class_alias(EventoFake::class, Evento::class);
            define('EVENTO_ALIAS_SET', true);
        }
        if (!defined('CLIENTE_ALIAS_SET')) {
            class_alias(ClienteFake::class, Cliente::class);
            define('CLIENTE_ALIAS_SET', true);
        }
        if (!defined('TICKET_ALIAS_SET')) {
            class_alias(TicketFake::class, Ticket::class);
            define('TICKET_ALIAS_SET', true);
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->ventaService = new VentaService();
    }


    public function testGenerarVentaSuccess()
    {
        $ticketDummy = new \stdClass();
        $ticketDummy->idEvent = 1;
        $ticketDummy->price   = 100;
        $ticketDummy->_id     = 'ticket-1';

        $ticketList = [$ticketDummy];
        $user = new User(['id' => 1]);
        $user = new User();
        $user->id = 1;

        $venta = $this->ventaService->generarVenta($ticketList, $user);

        $this->assertNotNull($venta, 'La venta debería haberse generado');
        $this->assertIsString($venta->guid);
        $this->assertNotEmpty($venta->lineasVenta);

        $expectedLinea = ['ticket-1', 100, 'Evento 1', '01-10-2025', '18:00:00', 'Madrid'];
        $this->assertEquals($expectedLinea, $venta->lineasVenta[0]);
    }


    public function testGenerarVentaFailsWhenEventNotFound()
    {
        $ticketDummy = new \stdClass();
        $ticketDummy->idEvent = 999;
        $ticketDummy->price   = 100;
        $ticketDummy->_id     = 'ticket-1';

        $ticketList = [$ticketDummy];
        $user = new User(['id' => 1]);

        Log::shouldReceive('error')
            ->once()
            ->with('Error al generar venta: El evento no existe');

        $venta = $this->ventaService->generarVenta($ticketList, $user);
        $this->assertNull($venta);
    }

    public function testGenerarTicketSuccess()
    {
        $user = new User(['id' => 2]);
        $ticket = $this->ventaService->generarTicket(1, $user);

        $this->assertNotNull($ticket, 'El ticket debería haberse generado');
        $this->assertEquals(1, $ticket->idEvent);
        $this->assertEquals(100, $ticket->price);
        $this->assertFalse($ticket->isReturned);
        $this->assertNotEmpty($ticket->_id);
    }


    public function testGenerarTicketFailsWhenEventNotFound()
    {
        $user = new User(['id' => 2]);

        Log::shouldReceive('error')
            ->once()
            ->with('Error al generar ticket de evento: El evento no existe');

        $ticket = $this->ventaService->generarTicket(999, $user);
        $this->assertNull($ticket);
    }

    public function testGenerarTicketFailsWhenClienteNotFound()
    {
        $user = new User();
        $user->id = -1;

        Log::shouldReceive('info')
            ->once()
            ->with('Error al generar ticket de evento: El cliente no existe');

        Log::shouldReceive('info')
            ->never()
            ->with('Cliente encontrado');

        $ticket = $this->ventaService->generarTicket(1, $user);
        $this->assertNull($ticket);
    }

    public function testGenerarTicketsSuccess()
    {
        $user = new User(['id' => 3]);

        // Simulamos un carrito con dos líneas: una con 2 tickets y otra con 1 ticket.
        $cart = [
            ['idEvent' => 1, 'quantity' => 2],
            ['idEvent' => 1, 'quantity' => 1],
        ];

        $tickets = $this->ventaService->generarTickets($cart, $user);
        $this->assertIsArray($tickets);
        $this->assertCount(3, $tickets);

        foreach ($tickets as $ticket) {
            $this->assertEquals(1, $ticket->idEvent);
            $this->assertEquals(100, $ticket->price);
        }
    }

     public function testGenerarTicketsFailsIfOneTicketFails()
    {
        $user = new User(['id' => 2]);

        // Carrito con dos items: uno correcto y otro con idEvent inválido.
        $cart = [
            ['idEvent' => 1, 'quantity' => 1],
            ['idEvent' => 999, 'quantity' => 1],
        ];

        // Para el primer ticket, se espera que se encuentre el cliente.
        Log::shouldReceive('info')
            ->once()
            ->with('Cliente encontrado');

        // Para el ticket con idEvent inválido, se espera que se loggee el error del evento faltante.
        Log::shouldReceive('error')
            ->once()
            ->with('Error al generar ticket de evento: El evento no existe');

        // Y luego, al detectar la falla, se espera otro log de error indicando la imposibilidad de generar el ticket.
        Log::shouldReceive('error')
            ->once()
            ->with('Error al generar ticket de evento: No se pudo generar el ticket');

        $tickets = $this->ventaService->generarTickets($cart, $user);
        $this->assertNull($tickets);
    }

}
