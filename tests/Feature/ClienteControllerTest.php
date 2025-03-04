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
        // Creamos un cliente en la base de datos
        $cliente = Cliente::factory()->create();
        // Forzamos a que la relación 'user' esté cargada si es necesaria en la vista
        $cliente->load('user');

        // Aseguramos que la caché esté limpia
        Cache::flush();

        // Realizamos la petición GET a la ruta 'clientes.show'
        $response = $this->get(route('clientes.show', $cliente->id));

        // Verificamos que se retorna la vista correcta con la variable 'cliente'
        $response->assertStatus(200)
            ->assertViewIs('clientes.show')
            ->assertViewHas('cliente');

        $clienteFromView = $response->viewData('cliente');
        $this->assertEquals($cliente->id, $clienteFromView->id);

        // Verificamos que se haya almacenado en caché con la key esperada
        $cacheKey = "cliente_{$cliente->id}";
        $cachedCliente = Cache::get($cacheKey);
        $this->assertNotNull($cachedCliente, "El cliente no fue almacenado en caché.");
        $this->assertEquals($cliente->id, $cachedCliente->id);
    }

    public function testShowUtilizaLaCache()
    {
        // Creamos un cliente en la base de datos
        $cliente = Cliente::factory()->create();
        $cliente->load('user'); // Aseguramos que la relación user esté disponible

        // Guardamos el cliente en la caché manualmente
        Cache::put("cliente_{$cliente->id}", $cliente, 60);

        // Simulamos que la base de datos no tiene clientes
        Cliente::where('id', $cliente->id)->delete();

        // Hacemos la petición
        $response = $this->get(route('clientes.show', $cliente->id));

        // Verificamos que el cliente proviene de la caché
        $response->assertStatus(200)
            ->assertViewIs('clientes.show')
            ->assertViewHas('cliente', $cliente);
    }

    public function testShowClienteNotFound()
    {
        // Elegimos un ID que no existe
        $nonexistentId = 9999;
        $response = $this->get(route('clientes.show', $nonexistentId));

        // Verificamos que se redirige a la ruta 'clientes.index' con el mensaje de error esperado
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

        $dni = '12345678X';
        $image = UploadedFile::fake()->image('dni.jpg');

        $data = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'password123',
            'dni' => $dni,
            'foto_dni' => $image,
        ];

        $response = $this->post(route('clientes.store'), $data);

        // Verificamos que se haya creado el usuario
        $this->assertDatabaseHas('users', [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
        ]);

        // Verificamos que se haya creado el cliente con el user_id correcto
        $user = User::where('email', 'juan@example.com')->first();
        $this->assertDatabaseHas('clientes', [
            'user_id' => $user->id,
            'dni' => $dni,
        ]);

        // Verificamos que la imagen se haya guardado
        Storage::disk('public')->assertExists("images/dni_{$dni}.jpg");

        // Verificamos que se haya enviado el correo
        Mail::assertSent(ClienteBienvenido::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });

        // Verificamos que el usuario tenga el rol de "cliente"
        $this->assertTrue($user->hasRole('cliente'));

        // Verificamos la redirección
        $response->assertRedirect(route('clientes.index'))
            ->assertSessionHas('success', 'Cliente creado con éxito');
    }

    public function testStoreDatosErroneos()
    {
        $response = $this->post(route('clientes.store'), [
            'name' => '',
            'email' => '',
            'password' => '',
            'dni' => '',
            'foto_dni' => null // Asegura que la validación del archivo se ejecute
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'password', 'dni', 'foto_dni']);
    }



    public function testStoreEmailExistente()
    {
        User::factory()->create(['email' => 'juan@example.com']);

        $data = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com', // Ya existe
            'password' => 'password123',
            'dni' => '12345678X',
            'foto_dni' => UploadedFile::fake()->image('dni.jpg'),
        ];

        $response = $this->post(route('clientes.store'), $data);

        $response->assertSessionHasErrors(['email']);
    }

    public function teststoreDniExistente()
    {
        Cliente::factory()->create(['dni' => '12345678X']);

        $response = $this->post(route('clientes.store'), [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'password123',
            'dni' => '12345678X',
            'foto_dni' => UploadedFile::fake()->image('dni.jpg')
        ]);

        $response->assertSessionHasErrors(['dni']);
    }

    public function testEditBuscaEnDBYGuerdaEnCache(){
        $cliente = Cliente::factory()->create();
        // Forzamos a que la relación 'user' esté cargada si es necesaria en la vista
        $cliente->load('user');

        // Aseguramos que la caché esté limpia
        Cache::flush();

        // Realizamos la petición GET a la ruta 'clientes.show'
        $response = $this->get(route('clientes.edit', $cliente->id));

        // Verificamos que se retorna la vista correcta con la variable 'cliente'
        $response->assertStatus(200)
            ->assertViewIs('clientes.edit')
            ->assertViewHas('cliente');

        $clienteFromView = $response->viewData('cliente');
        $this->assertEquals($cliente->id, $clienteFromView->id);

        // Verificamos que se haya almacenado en caché con la key esperada
        $cacheKey = "cliente_{$cliente->id}";
        $cachedCliente = Cache::get($cacheKey);
        $this->assertNotNull($cachedCliente, "El cliente no fue almacenado en caché.");
        $this->assertEquals($cliente->id, $cachedCliente->id);
    }

    public function testEditUtilizaLaCache()
    {
        // Creamos un cliente en la base de datos
        $cliente = Cliente::factory()->create();
        $cliente->load('user'); // Aseguramos que la relación user esté disponible

        // Guardamos el cliente en la caché manualmente
        Cache::put("cliente_{$cliente->id}", $cliente, 60);

        // Simulamos que la base de datos no tiene clientes
        Cliente::where('id', $cliente->id)->delete();

        // Hacemos la petición
        $response = $this->get(route('clientes.edit', $cliente->id));

        // Verificamos que el cliente proviene de la caché
        $response->assertStatus(200)
            ->assertViewIs('clientes.edit')
            ->assertViewHas('cliente', $cliente);
    }

    public function testEditClienteNotFound()
    {
        $response = $this->get(route('clientes.edit', 9999));
        $response->assertSessionHas('error', 'Cliente no encontrado');
    }

    public function testUpdateClienteSuccess()
    {
        $cliente = Cliente::factory()->create();
        $user = User::find($cliente->user_id);

        $nuevosDatos = [
            'name' => 'Nuevo Nombre',
            'email' => 'nuevo@example.com',
            'password' => 'nuevacontraseña123',
            'dni' => '87654321X',
            'foto_dni' => 'nueva/ruta/imagen.jpg'
        ];

        $response = $this->put(route('clientes.update', $cliente->id), $nuevosDatos);

        $response->assertRedirect(route('clientes.index'));
        $response->assertSessionHas('success');

        // Verifica la base de datos
        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
            'dni' => '87654321X',
            'foto_dni' => 'nueva/ruta/imagen.jpg'
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nuevo Nombre',
            'email' => 'nuevo@example.com'
        ]);

        // Verifica el caché
        $clienteCacheKey = "cliente_{$cliente->id}";
        $cachedCliente = Cache::get($clienteCacheKey);

        $this->assertNotNull($cachedCliente);
        $this->assertEquals('87654321X', $cachedCliente->dni);
        $this->assertEquals('nueva/ruta/imagen.jpg', $cachedCliente->foto_dni);
    }

    public function testUpdateDatosErroneos()
    {
        $cliente = Cliente::factory()->create();

        $datosInvalidos = [
            'dni' => '1234', // Formato DNI incorrecto
            'email' => 'no-es-un-email', // Email inválido
            'password' => 'corto', // Menos de 8 caracteres
            'name' => str_repeat('a', 256), // Excede máximo de 255 caracteres
            'foto_dni' => UploadedFile::fake()->create('documento.pdf') // No es una imagen
        ];

        $response = $this->put(route('clientes.update', $cliente->id), $datosInvalidos);

        $response->assertSessionHasErrors([
            'dni',
            'email',
            'password',
            'name',
            'foto_dni'
        ]);
    }


}
