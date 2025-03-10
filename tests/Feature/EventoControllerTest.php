<?php
namespace Tests\Feature;


use App\Models\Empresa;
use App\Models\Evento;
use App\Models\User;
use Database\Factories\EmpresaFactory;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EventoControllerTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        Evento::factory()->count(3)->create();

    }


    public function testGetAll()
    {
        $response = $this->get(route('eventos'));

        $response->assertStatus(200)
            ->assertViewIs('eventos.index')
            ->assertViewHas('eventos');

        $this->assertTrue($response->original->getData()['eventos']->count() > 0);
    }

    public function testIndex()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);
        $response = $this->get(route('eventos.index-admin'));
        $response->assertStatus(200);
        $response->assertViewIs('eventos.index-admin');
    }

    public function testShowEvento()
    {
        $evento = Evento::factory()->create();
        $response = $this->get(route('eventos.show', $evento->id));
        $response->assertStatus(200);
        $response->assertViewIs('eventos.show');
    }

    public function testShowEventoNotFound()
    {
        $response = $this->get(route('eventos.show', 999)); // ID que no existe
        $response->assertStatus(404);
    }

    public function testStoreEvento()
    {
        $user = User::factory()->create();
        $user->assignRole('empresa');
        $this->actingAs($user);

        $empresa = Empresa::factory()->create();

        $response = $this->post(route('eventos.store'), [
            'nombre' => 'Evento Test',
            'descripcion' => 'Descripción de prueba',
            'stock' => 10,
            'fecha' => now()->addDays(1)->format('Y-m-d'),
            'hora' => '12:00',
            'direccion' => 'Calle Falsa 123',
            'ciudad' => 'Ciudad Test',
            'precio' => 100.00,
            'foto' => UploadedFile::fake()->image('foto.jpg'),
            'empresa_id' => $empresa->id,
        ]);

        $response->assertRedirect(route('eventos.create'));
    }

    public function testStoreEventoLowerName()
    {
        $user = User::factory()->create();
        $user->assignRole('empresa');
        $this->actingAs($user);
        $response = $this->post(route('eventos.store'), [
            'nombre' => 'as', // Nombre vacío
            'stock' => 10,
            'fecha' => '2023-10-10',
            'hora' => '12:00',
            'direccion' => 'Calle Falsa 123',
            'ciudad' => 'Ciudad Test',
            'precio' => 100.00,
        ]);
        $response->assertRedirect(route('eventos.create'));
        $response->assertSessionHasErrors('nombre');
    }

    public function testStoreEventoUpperName()
    {
        $user = User::factory()->create();
        $user->assignRole('empresa');
        $this->actingAs($user);
        $response = $this->post(route('eventos.store'), [
            'nombre' => 'asaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', // Nombre vacío
            'stock' => 10,
            'fecha' => '2023-10-10',
            'hora' => '12:00',
            'direccion' => 'Calle Falsa 123',
            'ciudad' => 'Ciudad Test',
            'precio' => 100.00,
        ]);
        $response->assertRedirect(route('eventos.create'));
        $response->assertSessionHasErrors('nombre');
    }

    public function testStoreEventoEmptyName()
    {
        $user = User::factory()->create();
        $user->assignRole('empresa');
        $this->actingAs($user);
        $response = $this->post(route('eventos.store'), [
            'nombre' => '', // Nombre vacío
            'stock' => 10,
            'fecha' => '2023-10-10',
            'hora' => '12:00',
            'direccion' => 'Calle Falsa 123',
            'ciudad' => 'Ciudad Test',
            'precio' => 100.00,
        ]);
        $response->assertRedirect(route('eventos.create'));
        $response->assertSessionHasErrors('nombre');
    }

    public function testStoreEventoEmptyStock()
    {
        $user = User::factory()->create();
        $user->assignRole('empresa');
        $this->actingAs($user);
        $response = $this->post(route('eventos.store'), [
            'nombre' => 'Test',
            'stock' => '',
            'fecha' => '2023-10-10',
            'hora' => '12:00',
            'direccion' => 'Calle Falsa 123',
            'ciudad' => 'Ciudad Test',
            'precio' => 100.00,
        ]);
        $response->assertRedirect(route('eventos.create'));
        $response->assertSessionHasErrors('stock');
    }

    public function testStoreEventoEmptyDate()
    {
        $user = User::factory()->create();
        $user->assignRole('empresa');
        $this->actingAs($user);
        $response = $this->post(route('eventos.store'), [
            'nombre' => 'Test', // Nombre vacío
            'stock' => 10,
            'fecha' => '',
            'hora' => '12:00',
            'direccion' => 'Calle Falsa 123',
            'ciudad' => 'Ciudad Test',
            'precio' => 100.00,
        ]);
        $response->assertRedirect(route('eventos.create'));
        $response->assertSessionHasErrors('fecha');
    }

    public function testStoreEventoEmptyHora()
    {
        $user = User::factory()->create();
        $user->assignRole('empresa');
        $this->actingAs($user);
        $response = $this->post(route('eventos.store'), [
            'nombre' => 'Test', // Nombre vacío
            'stock' => 10,
            'fecha' => '2023-10-10',
            'hora' => '',
            'direccion' => 'Calle Falsa 123',
            'ciudad' => 'Ciudad Test',
            'precio' => 100.00,
        ]);
        $response->assertRedirect(route('eventos.create'));
        $response->assertSessionHasErrors('hora');
    }

    public function testStoreEventoUpperDireccion()
    {
        $user = User::factory()->create();
        $user->assignRole('empresa');
        $this->actingAs($user);
        $response = $this->post(route('eventos.store'), [
            'nombre' => 'Test', // Nombre vacío
            'stock' => 10,
            'fecha' => '2023-10-10',
            'hora' => '12:00',
            'direccion' => str_repeat('a', 300),
            'ciudad' => 'Ciudad Test',
            'precio' => 100.00,
            'foto' => UploadedFile::fake()->image('foto.jpg'),
        ]);
        $response->assertRedirect(route('eventos.create'));
        $response->assertSessionHasErrors('direccion');
    }

    public function testStoreEventoEmptyDireccion()
    {
        $user = User::factory()->create();
        $user->assignRole('empresa');
        $this->actingAs($user);
        $response = $this->post(route('eventos.store'), [
            'nombre' => 'Test', // Nombre vacío
            'stock' => 10,
            'fecha' => '2023-10-10',
            'hora' => '12:00',
            'direccion' => '',
            'ciudad' => 'Ciudad Test',
            'precio' => 100.00,
        ]);
        $response->assertRedirect(route('eventos.create'));
        $response->assertSessionHasErrors('direccion');
    }

    public function testStoreEventoUpperCiudad()
    {
        $user = User::factory()->create();
        $user->assignRole('empresa');
        $this->actingAs($user);
        $response = $this->post(route('eventos.store'), [
            'nombre' => '', // Nombre vacío
            'stock' => 10,
            'fecha' => '2023-10-10',
            'hora' => '12:00',
            'direccion' => 'Calle Falsa 123',
            'ciudad' => 'Ciudad Testaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
            'precio' => 100.00,
        ]);
        $response->assertRedirect(route('eventos.create'));
        $response->assertSessionHasErrors('ciudad');
    }

    public function testStoreEventoEmptyCiudad()
    {
        $user = User::factory()->create();
        $user->assignRole('empresa');
        $this->actingAs($user);
        $response = $this->post(route('eventos.store'), [
            'nombre' => '', // Nombre vacío
            'stock' => 10,
            'fecha' => '2023-10-10',
            'hora' => '12:00',
            'direccion' => 'Calle Falsa 123',
            'ciudad' => '',
            'precio' => 100.00,
        ]);
        $response->assertRedirect(route('eventos.create'));
        $response->assertSessionHasErrors('ciudad');
    }

    public function testStoreEventoBadPrecio()
    {
        $user = User::factory()->create();
        $user->assignRole('empresa');
        $this->actingAs($user);
        $response = $this->post(route('eventos.store'), [
            'nombre' => '', // Nombre vacío
            'stock' => 10,
            'fecha' => '2023-10-10',
            'hora' => '12:00',
            'direccion' => 'Calle Falsa 123',
            'ciudad' => 'Ciudad Test',
            'precio' => '',
        ]);
        $response->assertRedirect(route('eventos.create'));
        $response->assertSessionHasErrors('precio');
    }

    public function testUpdateEvento()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);
        $evento = Evento::factory()->create();
        $response = $this->put(route('eventos.update', $evento->id), [
            'nombre' => 'Evento Actualizado',
            'stock' => 20,
            'fecha' => '2023-10-10',
            'hora' => '12:00',
            'direccion' => 'Calle Actualizada 123',
            'ciudad' => 'Ciudad Actualizada',
            'precio' => 150.00,
        ]);
        $response->assertRedirect(route('eventos.edit', $evento->id));
    }

    public function testUpdateEventoLowerNombre()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);
        $evento = Evento::factory()->create();
        $response = $this->put(route('eventos.update', $evento->id), [
            'nombre' => 'as', // Nombre vacío
            'stock' => 20,
            'fecha' => '2023-10-10',
            'hora' => '12:00',
            'direccion' => 'Calle Actualizada 123',
            'ciudad' => 'Ciudad Actualizada',
            'precio' => 150.00,
        ]);
        $response->assertRedirect(route('eventos.edit', $evento->id));
        $response->assertSessionHasErrors('nombre');
    }

    public function testUpdateEventoUpperNombre()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);
        $evento = Evento::factory()->create();
        $response = $this->put(route('eventos.update', $evento->id), [
            'nombre' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', // Nombre vacío
            'stock' => 20,
            'fecha' => '2023-10-10',
            'hora' => '12:00',
            'direccion' => 'Calle Actualizada 123',
            'ciudad' => 'Ciudad Actualizada',
            'precio' => 150.00,
        ]);
        $response->assertRedirect(route('eventos.edit', $evento->id));
        $response->assertSessionHasErrors('nombre');
    }

    public function testUpdateEventoEmptyNombre()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);
        $evento = Evento::factory()->create();
        $response = $this->put(route('eventos.update', $evento->id), [
            'nombre' => '', // Nombre vacío
            'stock' => 20,
            'fecha' => '2023-10-10',
            'hora' => '12:00',
            'direccion' => 'Calle Actualizada 123',
            'ciudad' => 'Ciudad Actualizada',
            'precio' => 150.00,
        ]);
        $response->assertRedirect(route('eventos.edit', $evento->id));
        $response->assertSessionHasErrors('nombre');
    }

    public function testUpdateEventoBadStock()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);
        $evento = Evento::factory()->create();
        $response = $this->put(route('eventos.update', $evento->id), [
            'nombre' => 'Test', // Nombre vacío
            'stock' => '',
            'fecha' => '2023-10-10',
            'hora' => '12:00',
            'direccion' => 'Calle Actualizada 123',
            'ciudad' => 'Ciudad Actualizada',
            'precio' => 150.00,
        ]);
        $response->assertRedirect(route('eventos.edit', $evento->id));
        $response->assertSessionHasErrors('stock');
    }

    public function testUpdateEventoEmptyFecha()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);
        $evento = Evento::factory()->create();
        $response = $this->put(route('eventos.update', $evento->id), [
            'nombre' => 'Test', // Nombre vacío
            'stock' => 20,
            'fecha' => '',
            'hora' => '12:00',
            'direccion' => 'Calle Actualizada 123',
            'ciudad' => 'Ciudad Actualizada',
            'precio' => 150.00,
        ]);
        $response->assertRedirect(route('eventos.edit', $evento->id));
        $response->assertSessionHasErrors('fecha');
    }

    public function testUpdateEventoEmptyHora()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);
        $evento = Evento::factory()->create();
        $response = $this->put(route('eventos.update', $evento->id), [
            'nombre' => 'Test', // Nombre vacío
            'stock' => 20,
            'fecha' => '2023-10-10',
            'hora' => '',
            'direccion' => 'Calle Actualizada 123',
            'ciudad' => 'Ciudad Actualizada',
            'precio' => 150.00,
        ]);
        $response->assertRedirect(route('eventos.edit', $evento->id));
        $response->assertSessionHasErrors('hora');
    }

    public function testUpdateEventoUpperDireccion()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);
        $evento = Evento::factory()->create();
        $response = $this->put(route('eventos.update', $evento->id), [
            'nombre' => 'Test', // Nombre vacío
            'stock' => 20,
            'fecha' => '2023-10-10',
            'hora' => '12:00',
            'direccion' => 'Calle Actualizada 123aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
            'ciudad' => 'Ciudad Actualizada',
            'precio' => 150.00,
            'foto' => UploadedFile::fake()->image('foto.jpg'),
        ]);
        $response->assertRedirect(route('eventos.edit', $evento->id));
        $response->assertSessionHasErrors('direccion');
    }

    public function testUpdateEventoEmptyDireccion()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);
        $evento = Evento::factory()->create();
        $response = $this->put(route('eventos.update', $evento->id), [
            'nombre' => 'Test', // Nombre vacío
            'stock' => 20,
            'fecha' => '2023-10-10',
            'hora' => '12:00',
            'direccion' => '',
            'ciudad' => 'Ciudad Actualizada',
            'precio' => 150.00,
        ]);
        $response->assertRedirect(route('eventos.edit', $evento->id));
        $response->assertSessionHasErrors('direccion');
    }

    public function testUpdateEventoUpperCiudad()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);
        $evento = Evento::factory()->create();
        $response = $this->put(route('eventos.update', $evento->id), [
            'nombre' => 'Test', // Nombre vacío
            'stock' => 20,
            'fecha' => '2023-10-10',
            'hora' => '12:00',
            'direccion' => 'Calle Actualizada 123',
            'ciudad' => 'Ciudad Actualizadaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
            'precio' => 150.00,
        ]);
        $response->assertRedirect(route('eventos.edit', $evento->id));
        $response->assertSessionHasErrors('ciudad');
    }

    public function testUpdateEventoEmptyCiudad()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);
        $evento = Evento::factory()->create();
        $response = $this->put(route('eventos.update', $evento->id), [
            'nombre' => 'Test', // Nombre vacío
            'stock' => 20,
            'fecha' => '2023-10-10',
            'hora' => '12:00',
            'direccion' => 'Calle Actualizada 123',
            'ciudad' => '',
            'precio' => 150.00,
        ]);
        $response->assertRedirect(route('eventos.edit', $evento->id));
        $response->assertSessionHasErrors('ciudad');
    }

    public function testUpdateEventoNegativePrecio()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);
        $evento = Evento::factory()->create();
        $response = $this->put(route('eventos.update', $evento->id), [
            'nombre' => 'Test', // Nombre vacío
            'stock' => 20,
            'fecha' => '2023-10-10',
            'hora' => '12:00',
            'direccion' => 'Calle Actualizada 123',
            'ciudad' => 'Ciudad Actualizada',
            'precio' => -150.00,
        ]);
        $response->assertRedirect(route('eventos.edit', $evento->id));
        $response->assertSessionHasErrors('precio');
    }

    public function testUpdateEventoBadPrice()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);
        $evento = Evento::factory()->create();
        $response = $this->put(route('eventos.update', $evento->id), [
            'nombre' => 'Test', // Nombre vacío
            'stock' => 20,
            'fecha' => '2023-10-10',
            'hora' => '12:00',
            'direccion' => 'Calle Actualizada 123',
            'ciudad' => 'Ciudad Actualizada',
            'precio' => '',
        ]);
        $response->assertRedirect(route('eventos.edit', $evento->id));
        $response->assertSessionHasErrors('precio');
    }

    public function testDestroyEvento()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);
        $evento = Evento::factory()->create();
        $response = $this->delete(route('eventos.destroy', $evento->id));
        $response->assertRedirect(route('eventos'));
        $this->assertDatabaseMissing('eventos', ['id' => $evento->id]);
    }

    public function testDestroyEventoNotFound()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);

        $response = $this->delete(route('eventos.destroy', 999));

        $response->assertRedirect(route('eventos'));
        $response->assertSessionHas('error', 'Evento no encontrado');
    }


}
