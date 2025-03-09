<?php

namespace Tests\Feature;

use App\Mail\ClienteBienvenido;
use App\Models\Cliente;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;



class ClienteControllerTest extends TestCase
{
    use RefreshDatabase;


    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        Cliente::factory(5)->create();
    }


    public function testindex()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user);

        $response = $this->get(route('clientes.index'));

        $response->assertStatus(200)
            ->assertViewIs('clientes.index')
            ->assertViewHas('clientes');

        $clientes = $response->viewData('clientes');
        $clientes->load('user');

        foreach ($clientes as $cliente) {
            $this->assertNotNull($cliente->user, "El cliente con ID {$cliente->id} no tiene un usuario asociado.");
            $this->assertTrue($cliente->user->hasRole('cliente'), "El usuario asociado al cliente con ID {$cliente->id} no tiene el rol 'cliente'.");
        }
    }


    public function testShowCacheaDespuesDeLaPrimeraPeticion()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);

        $cliente = Cliente::factory()->create();
        $cliente->load('user');

        Cache::flush();

        $response = $this->get(route('clientes.show', $cliente->id));

        $response->assertStatus(200)
            ->assertViewIs('clientes.show')
            ->assertViewHas('cliente');

        $clienteFromView = $response->viewData('cliente');
        $this->assertEquals($cliente->id, $clienteFromView->id);

        $cacheKey = "cliente_{$cliente->id}";
        $cachedCliente = Cache::get($cacheKey);
        $this->assertNotNull($cachedCliente, "El cliente no fue almacenado en caché.");
        $this->assertEquals($cliente->id, $cachedCliente->id);
    }


    public function testShowUtilizaLaCache()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);

        $cliente = Cliente::factory()->create();
        $cliente->load('user');

        Cache::put("cliente_{$cliente->id}", $cliente, 60);

        Cliente::where('id', $cliente->id)->delete();

        $response = $this->get(route('clientes.show', $cliente->id));

        $response->assertStatus(200)
            ->assertViewIs('clientes.show')
            ->assertViewHas('cliente', $cliente);
    }


    public function testShowClienteNotFound()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);

        $nonexistentId = 9999;

        $response = $this->get(route('clientes.show', $nonexistentId));

        $response->assertRedirect(route('clientes.index'));
        $response->assertSessionHas('error', 'Cliente no encontrado');
    }


    public function testCreate()
    {
        $response = $this->get(route('clientes.create'));
        $response->assertStatus(200)
            ->assertViewIs('clientes.create');
    }

    public function testStoreValidoGuardaImagenEnviaCorreoTieneRolPermaneceAuth()
    {
        Storage::fake('public');
        Mail::fake();

        $image = UploadedFile::fake()->image('dni.jpg');

        $data = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'password123',
            'avatar' => $image,
        ];

        $response = $this->post(route('clientes.store'), $data);

        $this->assertDatabaseHas('users', [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
        ]);

        $user = User::where('email', 'juan@example.com')->first();
        $this->assertDatabaseHas('clientes', [
            'user_id' => $user->id,
        ]);

        $files = Storage::disk('public')->allFiles('images');
        $this->assertNotEmpty($files);

        $this->assertTrue(
            collect($files)->contains(function ($file) {
                return preg_match('/^images\/perfil_\d+\.(jpg|jpeg|png|gif|svg)$/', $file);
            })
        );

        Mail::assertSent(ClienteBienvenido::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });

        $this->assertTrue($user->hasRole('cliente'));

        $response->assertRedirect(route('eventos'))
            ->assertSessionHas('success', 'Cliente creado con éxito');
    }


    public function testStoreDatosErroneos()
    {
        $response = $this->post(route('clientes.store'), [
            'name' => '',
            'email' => '',
            'password' => '',
            'avatar' => null
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'password', 'avatar']);
    }



    public function testStoreEmailExistente()
    {
        User::factory()->create(['email' => 'juan@example.com']);

        $data = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com', // Ya existe
            'password' => 'password123',
            'avatar' => UploadedFile::fake()->image('dni.jpg'),
        ];

        $response = $this->post(route('clientes.store'), $data);

        $response->assertSessionHasErrors(['email']);
    }


    public function testEditBuscaEnDBYGuerdaEnCache()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);

        $cliente = Cliente::factory()->create();
        $cliente->load('user');

        Cache::flush();

        $response = $this->get(route('clientes.edit', $cliente->id));

        $response->assertStatus(200)
            ->assertViewIs('clientes.edit')
            ->assertViewHas('cliente');

        $clienteFromView = $response->viewData('cliente');
        $this->assertEquals($cliente->id, $clienteFromView->id);

        $cacheKey = "cliente_{$cliente->id}";
        $cachedCliente = Cache::get($cacheKey);
        $this->assertNotNull($cachedCliente, "El cliente no fue almacenado en caché.");
        $this->assertEquals($cliente->id, $cachedCliente->id);
    }


    public function testEditUtilizaLaCache()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);

        $cliente = Cliente::factory()->create();
        $cliente->load('user');

        Cache::put("cliente_{$cliente->id}", $cliente, 60);

        Cliente::where('id', $cliente->id)->delete();

        $response = $this->get(route('clientes.edit', $cliente->id));

        $response->assertStatus(200)
            ->assertViewIs('clientes.edit')
            ->assertViewHas('cliente', $cliente);
    }

    public function testEditClienteNotFound()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user);

        Cache::flush();

        $response = $this->get(route('clientes.edit', 9999));
        $response->assertRedirect(route('clientes.index'));

        $response->assertSessionHas('error', 'Cliente no encontrado');
    }



    public function testUpdateClienteSuccess()
    {
        $cliente = Cliente::factory()->create();
        $user = User::find($cliente->user_id);

        $user->assignRole('admin');

        $this->actingAs($user);

        Storage::fake('public');

        $nuevosDatos = [
            'name' => 'Nuevo Nombre',
            'email' => 'nuevo@example.com',
            'password' => 'nuevacontraseña123',
            'avatar' => UploadedFile::fake()->image('nuevo_avatar.jpg'),
        ];

        $response = $this->put(route('clientes.update', $cliente->id), $nuevosDatos);

        $response->assertRedirect(route('clientes.index'));
        $response->assertSessionHas('success');

        $nuevoNombreAvatar = 'perfil_Nuevo Nombre.jpg';
        Storage::disk('public')->assertExists("images/{$nuevoNombreAvatar}");

        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
            'avatar' => $nuevoNombreAvatar,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nuevo Nombre',
            'email' => 'nuevo@example.com',
        ]);

        $clienteCacheKey = "cliente_{$cliente->id}";
        $cachedCliente = Cache::get($clienteCacheKey);

        $this->assertNotNull($cachedCliente);
        $this->assertEquals($nuevoNombreAvatar, $cachedCliente->avatar);
    }


    public function testUpdateClienteNotFound()
    {
        $user = User::factory()->create()->assignRole('admin');
        $this->actingAs($user);

        $nuevosDatos = [
            'name' => 'Nuevo Nombre',
            'email' => 'nuevo@example.com',
        ];

        $response = $this->put(route('clientes.update', 9999), $nuevosDatos);

        $response->assertRedirect(route('clientes.index'));
        $response->assertSessionHas('error', 'Cliente no encontrado');
    }

    public function testUpdateClienteFailsDueToDuplicateEmail()
    {
        $cliente1 = Cliente::factory()->create();
        $cliente2 = Cliente::factory()->create();

        $user = User::find($cliente1->user_id);
        $user->assignRole('admin');
        $this->actingAs($user);

        $response = $this->put(route('clientes.update', $cliente1->id), [
            'email' => $cliente2->user->email,
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function testUpdateClienteWithoutAvatar()
    {
        $cliente = Cliente::factory()->create(['avatar' => 'perfil_actual.jpg']);
        $user = User::find($cliente->user_id);
        $user->assignRole('admin');
        $this->actingAs($user);

        $nuevosDatos = [
            'name' => 'Nombre Modificado',
            'email' => 'nuevo@example.com',
        ];

        $response = $this->put(route('clientes.update', $cliente->id), $nuevosDatos);

        $response->assertRedirect(route('clientes.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
            'avatar' => 'perfil_actual.jpg',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nombre Modificado',
            'email' => 'nuevo@example.com',
        ]);
    }


    public function testUpdateClienteWithInvalidData()
    {
        $cliente = Cliente::factory()->create();
        $user = User::find($cliente->user_id);

        $user->assignRole('admin');
        $this->actingAs($user);

        Storage::fake('public');

        $invalidData = [
            'name' => str_repeat('a', 300),
            'email' => 'correo-invalido',
            'password' => '123',
            'avatar' => UploadedFile::fake()->create('documento.pdf', 500), // Archivo no permitido
        ];

        $response = $this->put(route('clientes.update', $cliente->id), $invalidData);

        $response->assertSessionHasErrors(['name', 'email', 'password', 'avatar']);

        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
            'avatar' => $cliente->avatar,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $cliente->user->name,
            'email' => $cliente->user->email,
        ]);

        Storage::disk('public')->assertMissing('images/documento.pdf');
    }

    public function testDestroyClienteSuccess()
    {
        $cliente = Cliente::factory()->create();
        $user = User::find($cliente->user_id);

        Cache::put("cliente_{$cliente->id}", $cliente, 20);
        Cache::put("user_{$user->id}", $user, 20);

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);

        $response = $this->delete(route('clientes.destroy', $cliente->id));

        $response->assertRedirect(route('clientes.index'));
        $response->assertSessionHas('success', 'Cliente y usuario eliminados correctamente');

        $this->assertDatabaseMissing('clientes', ['id' => $cliente->id]);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);

        $this->assertNull(Cache::get("cliente_{$cliente->id}"));
        $this->assertNull(Cache::get("user_{$user->id}"));
    }

    public function testDestroyClienteNotFound()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);

        $response = $this->delete(route('clientes.destroy', 9999));

        $response->assertRedirect(route('clientes.index'));
        $response->assertSessionHas('error', 'Cliente no encontrado');
    }

}
