<?php

namespace Tests\Feature;

use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use MongoDB\BSON\ObjectId;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\Venta;
use Illuminate\Support\Facades\Cache;

class VentasApiControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp():void{
        parent::setUp();
        Ticket::truncate();  // Vacía la colección de Ticket
        Venta::truncate();   // Vacía la colección de Venta

        Ticket::factory(5)->create();
        Venta::factory()->count(1)->create();
        Venta::where('lineasVenta', 'size', 0)->delete();
    }

    #[Test]
    public function testIndexAllVentas()
    {
        $response = $this->getJson('/api/ventas');
        $response->assertStatus(200)
            ->assertJsonPath('data', fn($data) => count($data) === 1);
            /*->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'cliente',
                        'fecha',
                        'total',
                        'lineasVenta' => [
                            '*' => [
                                'id',
                                'ticket',
                                'descripcion',
                                'precio',
                                'cantidad',
                            ],
                        ],
                    ],
                ],
            ]
            );*/
    }
    #[Test]
    public function testShowVentaFoundInCache()
    {
        $venta = Venta::factory()->create();
        Cache::shouldReceive('get')->once()->with("venta_{$venta->id}")
            ->andReturn($venta);

        $response = $this->getJson("/api/ventas/{$venta->id}");

        $response->assertStatus(200)
            ->assertJson($venta->toArray());
    }

    #[Test]
    public function testShowVentaNotFoundInCacheButFoundInDatabase()
    {
        $venta = Venta::factory()->create();
        Cache::shouldReceive('get')->once()->with("venta_{$venta->id}")
            ->andReturn(null);
        Cache::shouldReceive('put')->once();

        $response = $this->getJson("/api/ventas/{$venta->id}");

        $response->assertStatus(200)
            ->assertJson($venta->toArray());
    }

    #[Test]
    public function testShowVentaNotFound()
    {
        $ventaId = (string) new ObjectId(); // ID inválido que no existe
        Cache::shouldReceive('get')->with("venta_{$ventaId}")->andReturn(null);

        $response = $this->getJson("/api/ventas/{$ventaId}");

        $response->assertStatus(404)
            ->assertJson(['message' => 'Venta not Found']);
    }

    #[Test]
    public function testShowVentaInvalidId()
    {
        $invalidId = 'invalid-id';

        $response = $this->getJson("/api/ventas/{$invalidId}");

        $response->assertStatus(404)
            ->assertJson(['message' => 'Venta not Found']);
    }
}
