<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\ClienteController;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Mockery;
use PHPUnit\Framework\TestCase;

class ClienteControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_all_clients()
    {
        Cliente::factory()->count(3)->create();

        $response = $this->getJson(route('clientes.index'));

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_show_returns_client()
    {
        $cliente = Cliente::factory()->create();

        $response = $this->getJson(route('clientes.show', $cliente->id));

        $response->assertStatus(200)
            ->assertJson(['id' => $cliente->id]);
    }

    public function test_search_by_dni()
    {
        $cliente = Cliente::factory()->create();

        $response = $this->getJson(route('clientes.searchByDni', $cliente->dni));

        $response->assertStatus(200)
            ->assertJsonStructure(['user', 'cliente']);
    }

    public function test_search_by_email()
    {
        $user = User::factory()->create();
        $cliente = Cliente::factory()->create();

        $response = $this->postJson(route('clientes.searchByEmail'), ['email' => $user->email]);

        $response->assertStatus(200)
            ->assertJsonStructure([['user', 'cliente']]);
    }

    public function test_store_creates_client()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'dni' => '12345678A',
            'foto_dni' => 'foto.jpg',
            'lista_entradas' => ['entrada1', 'entrada2']
        ];

        $response = $this->postJson('api/clientes', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([['user', 'cliente']]);
    }

    public function test_update_modifies_client()
    {
        $cliente = Cliente::factory()->create();

        $data = ['dni' => '87654321B'];

        $response = $this->putJson(route('clientes.update', $cliente->id), $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('clientes', ['id' => $cliente->id, 'dni' => '87654321B']);
    }

    public function test_destroy_soft_deletes_client()
    {
        $cliente = Cliente::factory()->create();
        $user = User::factory()->create(['id' => $cliente->user_id]);

        $response = $this->deleteJson(route('clientes.destroy', $cliente->id));

        $response->assertStatus(200);
        $this->assertDatabaseHas('clientes', ['id' => $cliente->id, 'is_deleted' => true]);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'is_deleted' => true]);
    }
}
