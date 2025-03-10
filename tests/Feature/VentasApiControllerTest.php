<?php

namespace Tests\Feature;

use App\Models\Evento;
use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use MongoDB\BSON\ObjectId;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\Venta;
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\Facades\JWTAuth;

class VentasApiControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeeder::class);

        Venta::query()->delete();

        $eventos = Evento::factory(5)->create();

        $eventos->each(function ($evento) {
            Ticket::factory(5)->create(['idEvent' => $evento->id]);
        });

        Venta::factory(3)->create();
    }
    public function testIndexAllVentas()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/ventas');

        $response->assertStatus(200)->assertJsonCount(3);
    }

    #[Test]
    public function testShowVentaFoundInCache()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);

        $venta = Venta::factory()->create();
        Cache::shouldReceive('get')->once()->with("venta_{$venta->id}")
            ->andReturn($venta);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson("/api/ventas/{$venta->id}");

        $response->assertStatus(200)
            ->assertJson($venta->toArray());
    }

    #[Test]
    public function testShowVentaNotFoundInCacheButFoundInDatabase()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);

        $venta = Venta::factory()->create();
        Cache::shouldReceive('get')->once()->with("venta_{$venta->id}")
            ->andReturn(null);
        Cache::shouldReceive('put')->once();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson("/api/ventas/{$venta->id}");

        $response->assertStatus(200)
            ->assertJson($venta->toArray());
    }

    #[Test]
    public function testShowVentaNotFound()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);

        $ventaId = (string) new ObjectId(); // ID inválido que no existe
        Cache::shouldReceive('get')->with("venta_{$ventaId}")->andReturn(null);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson("/api/ventas/{$ventaId}");

        $response->assertStatus(404)
            ->assertJson(['message' => 'Venta not Found']);
    }

    #[Test]
    public function testShowVentaInvalidId()
    {

        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);


        $invalidId = 'invalid-id';
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson("/api/ventas/{$invalidId}");

        $response->assertStatus(404)
            ->assertJson(['message' => 'Venta not Found']);
    }
}
