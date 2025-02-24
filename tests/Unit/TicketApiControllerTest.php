<?php

namespace Tests\Http\Controllers\Api;

use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class TicketApiControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Ticket::query()->delete();
        Ticket::factory(3)->create();
    }

    public function test_get_all_tickets()
    {
        $response = $this->getJson('/api/ticket');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }


    public function test_get_ticket_by_id_en_cache()
    {
        $ticket = Ticket::factory()->create();

        Cache::shouldReceive('get')
            ->once()
            ->with("ticket_{$ticket->id}")
            ->andReturn($ticket);

        $response = $this->getJson("/api/ticket/{$ticket->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $ticket->id
            ]);
    }

    public function test_get_ticket_by_id_in_database()
    {
        $ticket = Ticket::factory()->create();

        Cache::shouldReceive('get')
            ->once()
            ->with("ticket_{$ticket->id}")
            ->andReturn(null);

        Cache::shouldReceive('put')
            ->once()
            ->with("ticket_{$ticket->id}", Mockery::on(function ($arg) use ($ticket) {
                return $arg instanceof Ticket && $arg->id === $ticket->id;
            }), 20);

        $response = $this->getJson("/api/ticket/{$ticket->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $ticket->id
            ]);
    }

    public function test_get_ticket_id_not_in_cache()
    {
        Cache::shouldReceive('get')
            ->once()
            ->with("ticket_100")
            ->andReturn(null);

        $response = $this->getJson("/api/ticket/100");

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'Ticket not found'
        ]);
    }

    public function test_not_found_databases()
    {
        $response = $this->getJson('/api/ticket/100');

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'Ticket not found'
        ]);
    }

    public function test_create_ticket()
    {
        Ticket::factory()->make()->toArray();

        $ticketData = Ticket::factory()->make()->toArray();
        $ticketData['idEvent'] = '1234';
        $ticketData['idCLient'] = '1';
        $ticketData['price'] = '3';
        $ticketData['isReturned'] = false;

        $response = $this->postJson('/api/ticket', $ticketData);

        $response->assertStatus(201);
    }

    public function test_crear_no_puede_datos_invalidos()
    {
        $response = $this->postJson('/api/ticket', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['idEvent', 'idClient', 'price']);
    }


    public function test_destroy_ticket_found_in_cache()
    {
        $ticket = Ticket::factory()->create(['isReturned' => false]);

        $cacheKey = "ticket_{$ticket->id}";

        Cache::shouldReceive('get')
            ->once()
            ->with($cacheKey)
            ->andReturn($ticket);

        Cache::shouldReceive('forget')
            ->once()
            ->with($cacheKey);

        $response = $this->deleteJson("/api/ticket/{$ticket->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Ticket successfully returned',
                'ticket' => [
                    'id' => $ticket->id,
                    'isReturned' => true
                ]
            ]);
    }

    public function test_destroy_ticket_not_in_cache_but_in_database()
    {
        $ticket = Ticket::factory()->create(['isReturned' => false]);

        $cacheKey = "ticket_{$ticket->id}";

        Cache::shouldReceive('get')
            ->once()
            ->with($cacheKey)
            ->andReturn(null);

        Cache::shouldReceive('forget')
            ->once()
            ->with($cacheKey);

        $response = $this->deleteJson("/api/ticket/{$ticket->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Ticket successfully returned',
                'ticket' => [
                    'id' => $ticket->id,
                    'isReturned' => true
                ]
            ]);
    }

    public function test_destroy_ticket_not_in_cache_and_not_in_database()
    {
        $ticketId = 99999;

        $cacheKey = "ticket_{$ticketId}";

        Cache::shouldReceive('get')
            ->once()
            ->with($cacheKey)
            ->andReturn(null);

        $response = $this->deleteJson("/api/ticket/{$ticketId}");

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Ticket not found'
            ]);
    }


}
