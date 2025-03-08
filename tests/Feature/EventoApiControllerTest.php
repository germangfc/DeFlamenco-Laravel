<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\Evento;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class EventoApiControllerTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeeder::class);

        User::factory()->create();
    }

    public function testIndex()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $empresa = Empresa::factory()->create();
        Evento::create([
            'nombre' => 'Evento Test 1',
            'stock' => 100,
            'fecha' => '2025-02-17',
            'hora' => '14:00:00',
            'direccion' => 'Calle Falsa 123',
            'ciudad' => 'Madrid',
            'precio' => 19.99,
            'empresa_id' => $empresa->id
        ]);

        Evento::create([
            'nombre' => 'Evento Test 2',
            'stock' => 150,
            'fecha' => '2025-03-01',
            'hora' => '15:00:00',
            'direccion' => 'Nueva Calle 456',
            'ciudad' => 'Barcelona',
            'precio' => 29.99,
            'empresa_id' => $empresa->id
        ]);

        Evento::create([
            'nombre' => 'Evento Test 3',
            'stock' => 200,
            'fecha' => '2025-04-10',
            'hora' => '16:00:00',
            'direccion' => 'Otra Calle 789',
            'ciudad' => 'Valencia',
            'precio' => 39.99,
            'empresa_id' => $empresa->id
        ]);

        $response = $this->getJson('/api/eventos', [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200);

        $response->assertJsonCount(3);

        $response->assertJsonStructure([
            '*' => [
                'id', 'nombre', 'stock', 'fecha', 'hora', 'direccion', 'ciudad', 'precio', 'empresa_id'
            ],
        ]);
    }



    public function testStore()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $empresa = Empresa::factory()->create();
        $data = [
            'nombre' => 'Evento Test',
            'stock' => 100,
            'fecha' => '2025-02-17',
            'hora' => '14:00:00',
            'direccion' => 'Calle Falsa 123',
            'ciudad' => 'Madrid',
            'precio' => 19.99,
            'empresa_id' => $empresa->id,
        ];

        $response = $this->postJson('/api/eventos', $data, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(201)
            ->assertJson($data);

        $this->assertDatabaseHas('eventos', $data);
    }

    public function testStoreValidationError()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $empresa = Empresa::factory()->create();
        $data = [
            'nombre' => 'a',
            'stock' => 10,
            'fecha' => 'fecha-invalida',
            'hora' => '25:00:00',
            'direccion' => '',
            'ciudad' => '',
            'precio' => 'precio-invalido',
            'empresa_id' => $empresa->id,
        ];

        $response = $this->postJson('/api/eventos', $data, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(422);
    }



    public function testShow()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $empresa = Empresa::factory()->create();
        $evento = Evento::create([
            'nombre' => 'Evento Test',
            'stock' => 100,
            'fecha' => '2025-02-17',
            'hora' => '14:00:00',
            'direccion' => 'Calle Falsa 123',
            'ciudad' => 'Madrid',
            'precio' => 19.99,
            'empresa_id' => $empresa->id
        ]);

        $response = $this->getJson('/api/eventos/' . $evento->id, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $evento->id,
                'nombre' => $evento->nombre,
                'stock' => $evento->stock,
                'fecha' => $evento->fecha,
                'hora' => $evento->hora,
                'direccion' => $evento->direccion,
                'ciudad' => $evento->ciudad,
                'precio' => $evento->precio,
                'empresa_id' => $empresa->id
            ]);
    }



    public function testShowNotFound()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $response = $this->getJson('/api/eventos/999', [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(404)
            ->assertJson(['error' => 'Evento no encontrado']);
    }


    public function testUpdate()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $empresa = Empresa::factory()->create();
        $evento = Evento::create([
            'nombre' => 'Evento Test',
            'stock' => 100,
            'fecha' => '2025-02-17',
            'hora' => '14:00:00',
            'direccion' => 'Calle Falsa 123',
            'ciudad' => 'Madrid',
            'precio' => 19.99,
            'empresa_id' => $empresa->id
        ]);

        $data = [
            'nombre' => 'Evento Actualizado',
            'stock' => 150,
            'fecha' => '2025-03-01',
            'hora' => '15:00:00',
            'direccion' => 'Nueva Calle 456',
            'ciudad' => 'Barcelona',
            'precio' => 29.99,
            'empresa_id' => $empresa->id,
        ];

        $response = $this->putJson('/api/eventos/' . $evento->id, $data, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
            ->assertJson($data);

        $this->assertDatabaseHas('eventos', $data);
    }



    public function testUpdateNotFound()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $empresa = Empresa::factory()->create();
        $data = [
            'nombre' => 'Evento Actualizado',
            'stock' => 150,
            'fecha' => '2025-03-01',
            'hora' => '15:00:00',
            'direccion' => 'Nueva Calle 456',
            'ciudad' => 'Barcelona',
            'precio' => 29.99,
            'empresa_id' => $empresa->id,
        ];

        $response = $this->putJson('/api/eventos/999', $data, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(404)
            ->assertJson(['error' => 'Evento no encontrado']);
    }
    public function testUpdateValidationError()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $empresa = Empresa::factory()->create();
        $evento = Evento::create([
            'nombre' => 'Evento Actualizado',
            'stock' => 100,
            'fecha' => '2025-02-17',
            'hora' => '14:00:00',
            'direccion' => 'Calle Falsa 123',
            'ciudad' => 'Madrid',
            'precio' => 19.99,
            'empresa_id' => $empresa->id,
        ]);

        $data = [
            'nombre' => 'a',
            'stock' => -10,
            'fecha' => 'fecha-invalida',
            'hora' => '25:00:00',
            'direccion' => '',
            'ciudad' => '',
            'precio' => 'precio-invalido',
        ];

        $response = $this->putJson('/api/eventos/' . $evento->id, $data, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(422);

   }


    public function testDestroy()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $empresa = Empresa::factory()->create();
        $evento = Evento::create([
            'nombre' => 'Evento Test',
            'stock' => 100,
            'fecha' => '2025-02-17',
            'hora' => '14:00:00',
            'direccion' => 'Calle Falsa 123',
            'ciudad' => 'Madrid',
            'precio' => 19.99,
            'empresa_id' => $empresa->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->deleteJson('/api/eventos/' . $evento->id);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Evento eliminado']);

        $this->assertDatabaseMissing('eventos', [
            'id' => $evento->id,
        ]);
    }



    public function testDestroyNotFound()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->deleteJson('/api/eventos/112');


        $response->assertStatus(404)
            ->assertJson(['error' => 'Evento no encontrado']);
    }


    public function testGetByNombreConCache()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);

        $empresa = Empresa::factory()->create();
        $evento = Evento::create([
            'nombre' => 'Evento Test',
            'stock' => 100,
            'fecha' => '2025-02-17',
            'hora' => '14:00:00',
            'direccion' => 'Calle Falsa 123',
            'ciudad' => 'Madrid',
            'precio' => 19.99,
            'empresa_id' => $empresa->id
        ]);

        Cache::shouldReceive('get')
            ->once()
            ->with("eventos_nombre_{$evento->nombre}")
            ->andReturn(collect([$evento]));

        $response = $this->getJson('/api/eventos/nombre/' . $evento->nombre, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'nombre' => $evento->nombre,
                'stock' => $evento->stock,
                'fecha' => $evento->fecha,
                'hora' => $evento->hora,
                'direccion' => $evento->direccion,
                'ciudad' => $evento->ciudad,
                'precio' => $evento->precio,
                'empresa_id' => $empresa->id
            ]);
    }

    public function testGetByNombreNoEncontrado()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);


        $response = $this->getJson('/api/eventos/nombre/no_existe', [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'error' => 'Evento no encontrado'
            ]);
    }
}
