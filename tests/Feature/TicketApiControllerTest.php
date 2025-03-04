<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class TicketApiControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeeder::class);

        User::factory()->create();
        User::factory()->create();
        Ticket::query()->delete();
        Ticket::factory(3)->create();
    }

    public function test_get_all_tickets()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson("/api/tickets");

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }


    public function test_get_ticket_by_id_en_cache()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);

        $ticket = Ticket::factory()->create();

        Cache::shouldReceive('get')
            ->once()
            ->with("ticket_{$ticket->id}")
            ->andReturn($ticket);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson("/api/tickets/{$ticket->id}");

        $response->assertStatus(200)
            ->assertJson([
                '_id' => $ticket->id
            ]);
    }

    public function test_get_ticket_by_id_in_database()
    {
        $ticket = Ticket::factory()->create();
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);

        Cache::shouldReceive('get')
            ->once()
            ->with("ticket_{$ticket->id}")
            ->andReturn(null);

        Cache::shouldReceive('put')
            ->once()
            ->with("ticket_{$ticket->id}", Mockery::on(function ($arg) use ($ticket) {
                return $arg instanceof Ticket && $arg->id === $ticket->id;
            }), 20);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson("/api/tickets/{$ticket->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $ticket->id
            ]);
    }

    public function test_get_ticket_id_not_in_cache()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        Cache::shouldReceive('get')
            ->once()
            ->with("ticket_100")
            ->andReturn(null);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson("/api/tickets/100");

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'Ticket not found'
        ]);
    }

    public function test_not_found_databases()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson("/api/tickets/100");

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'Ticket not found'
        ]);
    }

    public function test_create_ticket()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        Ticket::factory()->make()->toArray();

        $ticketData = Ticket::factory()->make()->toArray();
        $ticketData['idEvent'] = '1234';
        $ticketData['idClient'] = '1';
        $ticketData['price'] = '3';
        $ticketData['isReturned'] = false;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson("/api/tickets", $ticketData);

        $response->assertStatus(201);
    }

    public function test_crear_no_puede_datos_invalidos()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson("/api/tickets", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['idEvent', 'idClient', 'price']);
    }


    public function test_destroy_ticket_found_in_cache()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $ticket = Ticket::factory()->create(['isReturned' => false]);

        $cacheKey = "ticket_{$ticket->id}";

        Cache::shouldReceive('get')
            ->once()
            ->with($cacheKey)
            ->andReturn($ticket);

        Cache::shouldReceive('forget')
            ->once()
            ->with($cacheKey);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->deleteJson("/api/tickets/{$ticket->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Ticket successfully returned',
                'ticket' => [
                    '_id' => $ticket->id,
                    'isReturned' => true
                ]
            ]);
    }

    public function test_destroy_ticket_not_in_cache_but_in_database()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $ticket = Ticket::factory()->create(['isReturned' => false]);

        $cacheKey = "ticket_{$ticket->id}";

        Cache::shouldReceive('get')
            ->once()
            ->with($cacheKey)
            ->andReturn(null);

        Cache::shouldReceive('forget')
            ->once()
            ->with($cacheKey);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->deleteJson("/api/tickets/{$ticket->id}");

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
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $ticketId = 99999;

        $cacheKey = "ticket_{$ticketId}";

        Cache::shouldReceive('get')
            ->once()
            ->with($cacheKey)
            ->andReturn(null);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->deleteJson("/api/tickets/{$ticketId}");

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Ticket not found'
            ]);
    }


}
