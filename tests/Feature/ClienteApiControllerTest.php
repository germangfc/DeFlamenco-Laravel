<?php

namespace Tests\Feature;

use App\Mail\ActualizacionDatos;
use App\Mail\ClienteBienvenido;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\User;
use Database\Seeders\EmpresasTableSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;
class ClienteApiControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeeder::class);

        User::factory()->create();
        User::factory()->create();
        Cliente::factory(5)->create();
        Mail::fake();
        Storage::fake('public');
    }

    public function test_getAll_clientes_success()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);

        $response = $this->getJson('/api/clientes', [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data', fn ($data) => count($data) === 5);
    }

    public function test_getAll_clientes_no_autorizado()
    {
        $user = User::factory()->create([
            'email' => 'harold08@example.net',
            'password' => bcrypt('password'),
            'tipo' => 'cliente'
        ]);
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/clientes');

        $response->assertStatus(403);
    }

    public function test_getById_Success_on_cache()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $cliente = Cliente::factory()->create();

        Cache::shouldReceive('get')
            ->once()
            ->with("cliente_{$cliente->id}")
            ->andReturn($cliente);

        $response = $this->getJson("/api/clientes/{$cliente->id}", [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $cliente->id,
            ]);

    }

    public function test_getById_no_in_cache() {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $cliente = Cliente::factory()->create();

        Cache::shouldReceive('get')
            ->once()
            ->with("cliente_{$cliente->id}")
            ->andReturn(null);

        Cache::shouldReceive('put')
            ->once()
            ->with("cliente_{$cliente->id}", Mockery::type('App\Models\Cliente'), 20)
            ->andReturn(true);

        $response = $this->getJson("/api/clientes/{$cliente->id}", [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $cliente->id,
            ]);
    }


    public function test_getById_not_found()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        Cache::shouldReceive('get')
            ->once()
            ->with("cliente_99999999")
            ->andReturnNull();

        $response = $this->getJson("/api/clientes/99999999", [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(404);
    }

    public function test_getById_no_autorizado(){
        $cliente = Cliente::factory()->create();
        $user = User::factory()->create([
            'email' => 'harold08@example.net',
            'password' => bcrypt('password'),
            'tipo' => 'cliente'
        ]);
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson("/api/clientes/{$cliente->id}");

        $response->assertStatus(403);
    }

    public function test_searchByEmail_success_on_cache()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $email = 'test@example.com';
        $user = User::factory()->create(['email' => $email]);
        $cliente = Cliente::factory()->create(['user_id' => $user->id]);

        Cache::shouldReceive('get')
            ->once()
            ->with("user_email_{$email}")
            ->andReturn($user);

        Cache::shouldReceive('get')
            ->once()
            ->with("cliente_user_{$email}")
            ->andReturn($cliente);

        $response = $this->getJson("/api/clientes/email/{$email}", [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $cliente->id,
            ]);
    }

    public function test_searchByEmail_not_found_user()
    {
        $email = 'test@example.com';
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);

        Cache::shouldReceive('get')
            ->once()
            ->with("user_email_{$email}")
            ->andReturn(null);

        $response = $this->getJson("/api/clientes/email/{$email}", [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Usuario no encontrado',
            ]);
    }

    public function test_searchByEmail_not_found_cliente()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $email = 'test@example.com';
        $user = User::factory()->create(['email' => $email]);

        Cache::shouldReceive('get')
            ->once()
            ->with("user_email_{$email}")
            ->andReturn($user);

        Cache::shouldReceive('get')
            ->once()
            ->with("cliente_user_{$email}")
            ->andReturn(null);

        $response = $this->getJson("/api/clientes/email/{$email}", [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Cliente no encontrado para este usuario',
            ]);
    }

    public function test_searchByEmail_no_cache()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $email = 'test@example.com';
        $user = User::factory()->create(['email' => $email]);
        $cliente = Cliente::factory()->create(['user_id' => $user->id]);

        Cache::shouldReceive('get')
            ->once()
            ->with("user_email_{$email}")
            ->andReturnNull();

        Cache::shouldReceive('put')
            ->once()
            ->with("user_email_{$email}", Mockery::on(function ($cachedUser) use ($user) {
                return $cachedUser->email === $user->email;
            }), 60)
            ->andReturn(true);

        Cache::shouldReceive('get')
            ->once()
            ->with("cliente_user_{$email}")
            ->andReturnNull();

        Cache::shouldReceive('put')
            ->once()
            ->with("cliente_user_{$email}", Mockery::on(function ($cachedCliente) use ($cliente) {
                return $cachedCliente->id === $cliente->id;
            }), Mockery::any());

        $response = $this->getJson("/api/clientes/email/{$email}", [
        'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $cliente->id,
            ]);
    }

    public function test_store_cliente_success()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);

        $payload = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password123',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/clientes', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                0 => [
                    'id',
                    'user_id',
                    'avatar',
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'johndoe@example.com',
        ]);



        Mail::assertSent(ClienteBienvenido::class, 1);
    }

    public function test_store_cliente_fails_validation()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);

        $payload = [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'short',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/clientes', $payload);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'errors' => [
                    'name',
                    'email',
                    'password',
                ],
            ]);
    }

    public function test_store_cliente_fails_duplicate_email()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);

        $existingUser = User::factory()->create([
            'email' => 'duplicate@example.com',
        ]);

        $payload = [
            'name' => 'John Doe',
            'email' => 'duplicate@example.com',
            'password' => 'password123',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/clientes', $payload);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'errors' => [
                    'email',
                ],
            ]);
    }

    public function test_store_cliente_no_autorizado()
    {
        $user = User::factory()->create([
            'email' => 'harold08@example.net',
            'password' => bcrypt('password'),
            'tipo' => 'cliente',
        ]);
        $token = JWTAuth::fromUser($user);

        $payload = [
            'name' => 'Unauthorized User',
            'email' => 'unauthorized@example.com',
            'password' => 'password123',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/clientes', $payload);

        $response->assertStatus(403);
    }

    public function test_update_cliente_success()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);

        $cliente = Cliente::factory()->create();
        $user = User::find($cliente->user_id);

        $payload = [
            'name' => 'Updated Name',
            'email' => 'updatedemail@example.com',
            'password' => 'newpassword123',
            'avatar' => 'foto_actualizada.jpg',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/clientes/{$cliente->id}", $payload);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $cliente->id,
                'avatar' => 'foto_actualizada.jpg',
            ]);

        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
            'avatar' => 'foto_actualizada.jpg',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updatedemail@example.com',
        ]);

        Mail::assertSent(ActualizacionDatos::class, 1);
    }

    public function test_update_cliente_fails_validation()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);

        $cliente = Cliente::factory()->create();

        $payload = [
            'email' => 'invalid-email',
            'password' => 'short',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/clientes/{$cliente->id}", $payload);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'errors' => [
                    'email',
                    'password',
                ],
            ]);
    }

    public function test_update_cliente_fails_duplicate_email()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);

        $existingUser = User::factory()->create([
            'email' => 'duplicate@example.com',
        ]);

        $cliente = Cliente::factory()->create();

        $payload = [
            'email' => 'duplicate@example.com', // Email repetido
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/clientes/{$cliente->id}", $payload);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'errors' => [
                    'email',
                ],
            ]);
    }


    public function test_update_cliente_not_found()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);

        $payload = [
            'name' => 'New Name',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/clientes/99999999", $payload);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Cliente no encontrado',
            ]);
    }

    public function test_update_cliente_no_autorizado()
    {
        $user = User::factory()->create([
            'email' => 'unauthorized@example.com',
            'password' => bcrypt('password'),
            'tipo' => 'cliente',
        ]);
        $token = JWTAuth::fromUser($user);

        $cliente = Cliente::factory()->create();

        $payload = [
            'name' => 'Unauthorized Name',
            'email' => 'unauthorizedupdate@example.com',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/clientes/{$cliente->id}", $payload);

        $response->assertStatus(403);
    }

    public function test_destroy_success()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);

        $cliente = Cliente::factory()->create();
        $user = User::factory()->create();
        $cliente->user_id = $user->id;
        $cliente->save();

        Cache::shouldReceive('get')
            ->once()
            ->with("cliente_{$cliente->id}")
            ->andReturn($cliente);
        Cache::shouldReceive('get')
            ->once()
            ->with("user_{$user->id}")
            ->andReturn($user);

        Cache::shouldReceive('forget')->twice();

        Mail::shouldReceive('to')
            ->once()
            ->with($user->email)
            ->andReturnSelf();
        Mail::shouldReceive('send')
            ->once();

        $response = $this->deleteJson("/api/clientes/{$cliente->id}", [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Cliente marcado como eliminado',
            ]);

        $this->assertTrue($cliente->is_deleted);
        $this->assertTrue($user->isDeleted);
    }

    public function test_destroy_cliente_no_encontrado()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);

        Cache::shouldReceive('get')
            ->once()
            ->with("cliente_9999")
            ->andReturn(null);

        $response = $this->deleteJson("/api/clientes/9999", [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Cliente no encontrado',
            ]);
    }

}
