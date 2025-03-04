<?php

namespace Tests\Feature;

use App\Models\Evento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventoApiControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        Evento::create([
            'nombre' => 'Evento Test 1',
            'stock' => 100,
            'fecha' => '2025-02-17',
            'hora' => '14:00:00',
            'direccion' => 'Calle Falsa 123',
            'ciudad' => 'Madrid',
            'precio' => 19.99,
        ]);

        Evento::create([
            'nombre' => 'Evento Test 2',
            'stock' => 150,
            'fecha' => '2025-03-01',
            'hora' => '15:00:00',
            'direccion' => 'Nueva Calle 456',
            'ciudad' => 'Barcelona',
            'precio' => 29.99,
        ]);

        Evento::create([
            'nombre' => 'Evento Test 3',
            'stock' => 200,
            'fecha' => '2025-04-10',
            'hora' => '16:00:00',
            'direccion' => 'Otra Calle 789',
            'ciudad' => 'Valencia',
            'precio' => 39.99,
        ]);

        $response = $this->getJson('/api/eventos');

        $response->assertStatus(200);

        $response->assertJsonCount(3);

        $response->assertJsonStructure([
            '*' => [
                'id', 'nombre', 'stock', 'fecha', 'hora', 'direccion', 'ciudad', 'precio',
            ],
        ]);
    }



    public function testStore()
    {
        $data = [
            'nombre' => 'Evento Test',
            'stock' => 100,
            'fecha' => '2025-02-17',
            'hora' => '14:00:00',
            'direccion' => 'Calle Falsa 123',
            'ciudad' => 'Madrid',
            'precio' => 19.99,
        ];

        $response = $this->postJson('/api/eventos', $data);

        $response->assertStatus(201)
            ->assertJson($data);

        $this->assertDatabaseHas('eventos', $data);
    }

    /*public function testStoreValidationError()
    {
        $data = [
            'nombre' => 'a',
            'stock' => 10,
            'fecha' => 'fecha-invalida',
            'hora' => '25:00:00',
            'direccion' => '',
            'ciudad' => '',
            'precio' => 'precio-invalido',
        ];

        $response = $this->postJson('/api/eventos', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'nombre', 'fecha', 'hora', 'direccion', 'ciudad', 'precio',
            ]);
    }
    */



    public function testShow()
    {
        $evento = Evento::create([
            'nombre' => 'Evento Test',
            'stock' => 100,
            'fecha' => '2025-02-17',
            'hora' => '14:00:00',
            'direccion' => 'Calle Falsa 123',
            'ciudad' => 'Madrid',
            'precio' => 19.99,
        ]);

        $response = $this->getJson('/api/eventos/' . $evento->id);

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
            ]);
    }



    public function testShowNotFound()
    {
        $response = $this->getJson('/api/eventos/999');

        $response->assertStatus(404)
            ->assertJson(['error' => 'Evento no encontrado']);
    }


    public function testUpdate()
    {
        $evento = Evento::create([
            'nombre' => 'Evento Test',
            'stock' => 100,
            'fecha' => '2025-02-17',
            'hora' => '14:00:00',
            'direccion' => 'Calle Falsa 123',
            'ciudad' => 'Madrid',
            'precio' => 19.99,
        ]);

        $data = [
            'nombre' => 'Evento Actualizado',
            'stock' => 150,
            'fecha' => '2025-03-01',
            'hora' => '15:00:00',
            'direccion' => 'Nueva Calle 456',
            'ciudad' => 'Barcelona',
            'precio' => 29.99,
        ];

        $response = $this->putJson('/api/eventos/' . $evento->id, $data);

        $response->assertStatus(200)
            ->assertJson($data);

        $this->assertDatabaseHas('eventos', $data);
    }



    public function testUpdateNotFound()
    {
        $data = [
            'nombre' => 'Evento Actualizado',
            'stock' => 150,
            'fecha' => '2025-03-01',
            'hora' => '15:00:00',
            'direccion' => 'Nueva Calle 456',
            'ciudad' => 'Barcelona',
            'precio' => 29.99,
        ];

        $response = $this->putJson('/api/eventos/999', $data);

        $response->assertStatus(404)
            ->assertJson(['error' => 'Evento no encontrado']);
    }

   /* public function testUpdateValidationError()
    {
        $evento = Evento::create([
            'nombre' => 'Evento Actualizado',
            'stock' => 100,
            'fecha' => '2025-02-17',
            'hora' => '14:00:00',
            'direccion' => 'Calle Falsa 123',
            'ciudad' => 'Madrid',
            'precio' => 19.99,
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

        $response = $this->putJson('/api/eventos/' . $evento->id, $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'nombre', 'stock', 'fecha', 'hora', 'direccion', 'ciudad', 'precio',
            ]);

   }
*/


    public function testDestroy()
    {
        $evento = Evento::create([
            'nombre' => 'Evento Test',
            'stock' => 100,
            'fecha' => '2025-02-17',
            'hora' => '14:00:00',
            'direccion' => 'Calle Falsa 123',
            'ciudad' => 'Madrid',
            'precio' => 19.99,
        ]);

        $response = $this->deleteJson('/api/eventos/' . $evento->id);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Evento eliminado']);

        $this->assertDatabaseMissing('eventos', [
            'id' => $evento->id,
        ]);
    }



    public function testDestroyNotFound()
    {
        $response = $this->deleteJson('/api/eventos/999');

        $response->assertStatus(404)
            ->assertJson(['error' => 'Evento no encontrado']);
    }


    public function testGetByNombre()
    {
        Evento::create([
            'nombre' => 'Evento Test',
            'stock' => 100,
            'fecha' => '2025-02-17',
            'hora' => '14:00:00',
            'direccion' => 'Calle Falsa 123',
            'ciudad' => 'Madrid',
            'precio' => 19.99,
        ]);

        Evento::create([
            'nombre' => 'Evento Test',
            'stock' => 150,
            'fecha' => '2025-03-01',
            'hora' => '15:00:00',
            'direccion' => 'Nueva Calle 456',
            'ciudad' => 'Barcelona',
            'precio' => 29.99,
        ]);

        Evento::create([
            'nombre' => 'Evento Test',
            'stock' => 200,
            'fecha' => '2025-04-10',
            'hora' => '16:00:00',
            'direccion' => 'Otra Calle 789',
            'ciudad' => 'Valencia',
            'precio' => 39.99,
        ]);

        $response = $this->getJson('/api/eventos/nombre/Evento Test');

        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => [
                    'id', 'nombre', 'stock', 'fecha', 'hora', 'direccion', 'ciudad', 'precio',
                ],
            ]);
    }


    public function testGetByNombreNotFound()
    {
        $response = $this->getJson('/api/eventos/nombre/NoExiste');

        $response->assertStatus(404)
            ->assertJson(['error' => 'Evento no encontrado']);
    }

}
