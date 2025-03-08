<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmpresaControllerTest extends TestCase
{
    use RefreshDatabase; // Reinicia la base de datos en cada test

    protected function setUp(): void
    {
        parent::setUp(); // Inicializa Laravel

        $this->seed(UserSeeder::class);

        // Aquí puedes usar Eloquent
        User::factory()->create();
        User::factory()->create();
        Empresa::factory(5)->create(); // Crea 5 empresas en la base de datos
    }

    public function test_database_connection()
    {
        //dump(config('database.connections.pgsql.database'));
        $this->assertEquals('testing', config('database.connections.pgsql.database'));
    }


    public function testIndex()
    {

        $response = $this->get(route('empresas.index-admin'));

        $response->assertStatus(200)
            ->assertViewIs('empresas.user')
            ->assertViewHas('empresas');
    }



    public function testShow()
    {

        // Recupera una empresa existente desde la base de datos
        $empresa = Empresa::first();

        $response = $this->get(route('empresas.show', $empresa->id));

        $response->assertStatus(200)
            ->assertViewIs('empresas.show')
            ->assertViewHas('empresa', $empresa);
    }


    public function testShowNotFound()
    {
        $response = $this->get(route('empresas.show', 999));

        $response->assertRedirect(route('empresas.index'))
            ->assertSessionHas('error', 'Empresa no encontrada');
    }


    public function testShowByCif()
    {

        Empresa::factory()->create([
            'cif' => 'B12345678',
            'name' => 'Empresa Test',
        ]);

        // Recupera la empresa por su CIF
        $empresa = Empresa::where('cif', 'B12345678')->first();

        $response = $this->get(route('empresas.showByCif', 'B12345678'));

        $response->assertRedirect(route('empresas.show', ['id' => $empresa->id]));
    }

    public function testShowByCifNotFound()
    {

        $response = $this->get(route('empresas.showByCif', 'B12345679'));

        $response->assertRedirect(route('empresas.index'))
            ->assertSessionHas('error', 'Empresa no encontrada');
    }

    public function testShowByNombre()
    {

        Empresa::factory()->create([
            'name' => 'Empresa Ejemplo 1',
            'cif' => 'B87654321',
        ]);

        // Recupera la empresa por su nombre
        $empresa = Empresa::where('name', 'Empresa Ejemplo 1')->first();

        $response = $this->get(route('empresas.showByNombre', 'Empresa Ejemplo 1'));

        $response->assertRedirect(route('empresas.show', ['id' => $empresa->id]));
    }


    public function testShowByNombreNotFound()
    {
        $response = $this->get(route('empresas.showByNombre', 'Empresa Inexistente'));

        $response->assertRedirect(route('empresas.index'))
            ->assertSessionHas('error', 'Empresa no encontrada');
    }

    public function testStore()
    {
        Storage::fake('public'); // Fake para simular el almacenamiento

        $file = UploadedFile::fake()->image('empresa.jpg');

        $data = [
            // Datos del usuario
            'name' => 'Empresa Test',
            'email' => 'empresa@test.com',
            'password' => 'password123', // Se requiere para crear el usuario

            // Datos de la empresa
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES1234567890123456789012', // Se corrigió el formato
            'telefono' => '600123456',
            'imagen' => $file,
        ];

        $response = $this->post(route('empresas.store'), $data);

        $response->assertRedirect(route('empresas.index'))
            ->assertSessionHas('success', 'Empresa creada con éxito');

        // Verificar que el usuario se ha guardado en la base de datos
        $this->assertDatabaseHas('users', [
            'name' => 'Empresa Test',
            'email' => 'empresa@test.com',
        ]);

        // Verificar que la empresa se ha guardado en la base de datos
        $this->assertDatabaseHas('empresas', [
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 123',
            'telefono' => '600123456',
            'cuentaBancaria' => 'ES1234567890123456789012',
        ]);

        // 🔹 Ajustar la verificación de la imagen con el mismo nombre que se usa en el controlador
        $customName = 'empresa_' . str_replace(' ', '_', strtolower($data['name'])) . '.' . $file->getClientOriginalExtension();

        Storage::disk('public')->assertExists('empresas/' . $customName);
    }


    public function testStoreBadName()
    {
        Storage::fake('public'); // Fake para simular almacenamiento

        $file = UploadedFile::fake()->image('empresa.jpg');

        $data = [
            // Datos del usuario
            'name' => '',
            'email' => 'empresa@test.com',
            'password' => 'password123', // Se requiere para crear el usuario

            // Datos de la empresa
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012', // Se corrigió el formato
            'telefono' => '600123456',
            'imagen' => $file,
        ];

        $response = $this->post(route('empresas.store'), $data);

        $response->assertSessionHasErrors(['name']);

        // Verificar que no se creó la empresa ni el usuario en la BD
        $this->assertDatabaseMissing('empresas', ['cif' => 'B12345678']);
    }

    public function testStoreBadPassword()
    {
        Storage::fake('public'); // Fake para simular almacenamiento

        $file = UploadedFile::fake()->image('empresa.jpg');

        $data = [
            // Datos del usuario
            'name' => 'Empresa Test',
            'email' => 'empresa@test.com',
            'password' => 'passwor', // Se requiere para crear el usuario

            // Datos de la empresa
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012', // Se corrigió el formato
            'telefono' => '600123456',
            'imagen' => $file,
        ];

        $response = $this->post(route('empresas.store'), $data);

        $response->assertSessionHasErrors(['password']);

        // Verificar que no se creó la empresa ni el usuario en la BD
        $this->assertDatabaseMissing('users', ['name' => 'Empresa Test']);
        $this->assertDatabaseMissing('empresas', ['cif' => 'B12345678']);
    }

    public function testStoreEmptyPassword()
    {
        Storage::fake('public'); // Fake para simular almacenamiento

        $file = UploadedFile::fake()->image('empresa.jpg');

        $data = [
            // Datos del usuario
            'name' => 'Empresa Test',
            'email' => 'empresa@test.com',
            'password' => '', // Se requiere para crear el usuario

            // Datos de la empresa
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012', // Se corrigió el formato
            'telefono' => '600123456',
            'imagen' => $file,
        ];

        $response = $this->post(route('empresas.store'), $data);

        $response->assertSessionHasErrors(['password']);

        // Verificar que no se creó la empresa ni el usuario en la BD
        $this->assertDatabaseMissing('users', ['name' => 'Empresa Test']);
        $this->assertDatabaseMissing('empresas', ['cif' => 'B12345678']);
    }

    public function testStoreBadEmail()
    {
        Storage::fake('public'); // Fake para simular almacenamiento

        $file = UploadedFile::fake()->image('empresa.jpg');

        $data = [
            // Datos del usuario
            'name' => 'Empresa Test',
            'email' => 'badEmail',
            'password' => 'password123', // Se requiere para crear el usuario

            // Datos de la empresa
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012', // Se corrigió el formato
            'telefono' => '600123456',
            'imagen' => $file,
        ];

        $response = $this->post(route('empresas.store'), $data);

        $response->assertSessionHasErrors(['email']);

        // Verificar que no se creó la empresa ni el usuario en la BD
        $this->assertDatabaseMissing('users', ['name' => 'Empresa Test']);
        $this->assertDatabaseMissing('empresas', ['cif' => 'B12345678']);
    }

    public function testStoreEmptyEmail()
    {
        Storage::fake('public'); // Fake para simular almacenamiento

        $file = UploadedFile::fake()->image('empresa.jpg');

        $data = [
            // Datos del usuario
            'name' => 'Empresa Test',
            'email' => '',
            'password' => 'password123', // Se requiere para crear el usuario

            // Datos de la empresa
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012', // Se corrigió el formato
            'telefono' => '600123456',
            'imagen' => $file,
        ];

        $response = $this->post(route('empresas.store'), $data);

        $response->assertSessionHasErrors(['email']);

        // Verificar que no se creó la empresa ni el usuario en la BD
        $this->assertDatabaseMissing('users', ['name' => 'Empresa Test']);
        $this->assertDatabaseMissing('empresas', ['cif' => 'B12345678']);
    }

    public function testStoreBadCif()
    {
        Storage::fake('public'); // Fake para simular almacenamiento

        $file = UploadedFile::fake()->image('empresa.jpg');

        $data = [
            // Datos del usuario
            'name' => 'Empresa Test',
            'email' => 'empresa@test.com',
            'password' => 'password123', // Se requiere para crear el usuario

            // Datos de la empresa
            'cif' => '012345678',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012', // Se corrigió el formato
            'telefono' => '600123456',
            'imagen' => $file,
        ];

        $response = $this->post(route('empresas.store'), $data);

        $response->assertSessionHasErrors(['cif']);

        // Verificar que no se creó la empresa ni el usuario en la BD
        $this->assertDatabaseMissing('users', ['name' => 'Empresa Test']);
        $this->assertDatabaseMissing('empresas', ['cif' => 'B12345678']);
    }

    public function testStoreBadCifLower()
    {
        Storage::fake('public'); // Fake para simular almacenamiento

        $file = UploadedFile::fake()->image('empresa.jpg');

        $data = [
            // Datos del usuario
            'name' => 'Empresa Test',
            'email' => 'empresa@test.com',
            'password' => 'password123', // Se requiere para crear el usuario

            // Datos de la empresa
            'cif' => 'B1234567',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012', // Se corrigió el formato
            'telefono' => '600123456',
            'imagen' => $file,
        ];

        $response = $this->post(route('empresas.store'), $data);

        $response->assertSessionHasErrors(['cif']);

        // Verificar que no se creó la empresa ni el usuario en la BD
        $this->assertDatabaseMissing('users', ['name' => 'Empresa Test']);
        $this->assertDatabaseMissing('empresas', ['cif' => 'B12345678']);
    }


    public function testStoreBadCifUpper()
    {
        Storage::fake('public'); // Fake para simular almacenamiento

        $file = UploadedFile::fake()->image('empresa.jpg');

        $data = [
            // Datos del usuario
            'name' => 'Empresa Test',
            'email' => 'empresa@test.com',
            'password' => 'password123', // Se requiere para crear el usuario

            // Datos de la empresa
            'cif' => 'B123456789',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012', // Se corrigió el formato
            'telefono' => '600123456',
            'imagen' => $file,
        ];

        $response = $this->post(route('empresas.store'), $data);

        $response->assertSessionHasErrors(['cif']);

        // Verificar que no se creó la empresa ni el usuario en la BD
        $this->assertDatabaseMissing('users', ['name' => 'Empresa Test']);
        $this->assertDatabaseMissing('empresas', ['cif' => 'B12345678']);
    }


    public function testStoreEmptyCif()
    {
        Storage::fake('public'); // Fake para simular almacenamiento

        $file = UploadedFile::fake()->image('empresa.jpg');

        $data = [
            // Datos del usuario
            'name' => 'Empresa Test',
            'email' => 'empresa@test.com',
            'password' => 'password123', // Se requiere para crear el usuario

            // Datos de la empresa
            'cif' => '',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012', // Se corrigió el formato
            'telefono' => '600123456',
            'imagen' => $file,
        ];

        $response = $this->post(route('empresas.store'), $data);

        $response->assertSessionHasErrors(['cif']);

        // Verificar que no se creó la empresa ni el usuario en la BD
        $this->assertDatabaseMissing('users', ['name' => 'Empresa Test']);
        $this->assertDatabaseMissing('empresas', ['cif' => 'B12345678']);
    }

    public function testStoreBadDireccion()
    {
        Storage::fake('public'); // Fake para simular almacenamiento

        $file = UploadedFile::fake()->image('empresa.jpg');

        $data = [
            // Datos del usuario
            'name' => 'Empresa Test',
            'email' => 'empresa@test.com',
            'password' => 'password123', // Se requiere para crear el usuario

            // Datos de la empresa
            'cif' => 'B12345678',
            'direccion' => '',
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012', // Se corrigió el formato
            'telefono' => '600123456',
            'imagen' => $file,
        ];

        $response = $this->post(route('empresas.store'), $data);

        $response->assertSessionHasErrors(['direccion']);

        // Verificar que no se creó la empresa ni el usuario en la BD
        $this->assertDatabaseMissing('users', ['name' => 'Empresa Test']);
        $this->assertDatabaseMissing('empresas', ['cif' => 'B12345678']);
    }

    public function testStoreBadCuentaBancaria()
    {
        Storage::fake('public'); // Fake para simular almacenamiento

        $file = UploadedFile::fake()->image('empresa.jpg');

        $data = [
            // Datos del usuario
            'name' => 'Empresa Test',
            'email' => 'empresa@test.com',
            'password' => 'password123', // Se requiere para crear el usuario

            // Datos de la empresa
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'FR12 3456 7890 1234 5678 9012', // Se corrigió el formato
            'telefono' => '600123456',
            'imagen' => $file,
        ];

        $response = $this->post(route('empresas.store'), $data);

        $response->assertSessionHasErrors(['cuentaBancaria']);

        // Verificar que no se creó la empresa ni el usuario en la BD
        $this->assertDatabaseMissing('users', ['name' => 'Empresa Test']);
        $this->assertDatabaseMissing('empresas', ['cif' => 'B12345678']);
    }

    public function testStoreBadCuentaBancariaLower()
    {
        Storage::fake('public'); // Fake para simular almacenamiento

        $file = UploadedFile::fake()->image('empresa.jpg');

        $data = [
            // Datos del usuario
            'name' => 'Empresa Test',
            'email' => 'empresa@test.com',
            'password' => 'password123', // Se requiere para crear el usuario

            // Datos de la empresa
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 901', // Se corrigió el formato
            'telefono' => '600123456',
            'imagen' => $file,
        ];

        $response = $this->post(route('empresas.store'), $data);

        $response->assertSessionHasErrors(['cuentaBancaria']);

        // Verificar que no se creó la empresa ni el usuario en la BD
        $this->assertDatabaseMissing('users', ['name' => 'Empresa Test']);
        $this->assertDatabaseMissing('empresas', ['cif' => 'B12345678']);
    }


    public function testStoreBadCuentaBancariaUpper()
    {
        Storage::fake('public'); // Fake para simular almacenamiento

        $file = UploadedFile::fake()->image('empresa.jpg');

        $data = [
            // Datos del usuario
            'name' => 'Empresa Test',
            'email' => 'empresa@test.com',
            'password' => 'password123', // Se requiere para crear el usuario

            // Datos de la empresa
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 90123', // Se corrigió el formato
            'telefono' => '600123456',
            'imagen' => $file,
        ];

        $response = $this->post(route('empresas.store'), $data);

        $response->assertSessionHasErrors(['cuentaBancaria']);

        // Verificar que no se creó la empresa ni el usuario en la BD
        $this->assertDatabaseMissing('users', ['name' => 'Empresa Test']);
        $this->assertDatabaseMissing('empresas', ['cif' => 'B12345678']);
    }

    public function testStoreEmptyCuentaBancaria()
    {
        Storage::fake('public'); // Fake para simular almacenamiento

        $file = UploadedFile::fake()->image('empresa.jpg');

        $data = [
            // Datos del usuario
            'name' => 'Empresa Test',
            'email' => 'empresa@test.com',
            'password' => 'password123', // Se requiere para crear el usuario

            // Datos de la empresa
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => '', // Se corrigió el formato
            'telefono' => '600123456',
            'imagen' => $file,
        ];

        $response = $this->post(route('empresas.store'), $data);

        $response->assertSessionHasErrors(['cuentaBancaria']);

        // Verificar que no se creó la empresa ni el usuario en la BD
        $this->assertDatabaseMissing('users', ['name' => 'Empresa Test']);
        $this->assertDatabaseMissing('empresas', ['cif' => 'B12345678']);
    }

    public function testStoreBadTelefono()
    {
        Storage::fake('public'); // Fake para simular almacenamiento

        $file = UploadedFile::fake()->image('empresa.jpg');

        $data = [
            // Datos del usuario
            'name' => 'Empresa Test',
            'email' => 'empresa@test.com',
            'password' => 'password123', // Se requiere para crear el usuario

            // Datos de la empresa
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012', // Se corrigió el formato
            'telefono' => '100123456',
            'imagen' => $file,
        ];

        $response = $this->post(route('empresas.store'), $data);

        $response->assertSessionHasErrors(['telefono']);

        // Verificar que no se creó la empresa ni el usuario en la BD
        $this->assertDatabaseMissing('users', ['name' => 'Empresa Test']);
        $this->assertDatabaseMissing('empresas', ['cif' => 'B12345678']);
    }

    public function testStoreBadTelefonoLower()
    {
        Storage::fake('public'); // Fake para simular almacenamiento

        $file = UploadedFile::fake()->image('empresa.jpg');

        $data = [
            // Datos del usuario
            'name' => 'Empresa Test',
            'email' => 'empresa@test.com',
            'password' => 'password123', // Se requiere para crear el usuario

            // Datos de la empresa
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012', // Se corrigió el formato
            'telefono' => '60012345',
            'imagen' => $file,
        ];

        $response = $this->post(route('empresas.store'), $data);

        $response->assertSessionHasErrors(['telefono']);

        // Verificar que no se creó la empresa ni el usuario en la BD
        $this->assertDatabaseMissing('users', ['name' => 'Empresa Test']);
        $this->assertDatabaseMissing('empresas', ['cif' => 'B12345678']);
    }

    public function testStoreBadTelefonoUpper()
    {
        Storage::fake('public'); // Fake para simular almacenamiento

        $file = UploadedFile::fake()->image('empresa.jpg');

        $data = [
            // Datos del usuario
            'name' => 'Empresa Test',
            'email' => 'empresa@test.com',
            'password' => 'password123', // Se requiere para crear el usuario

            // Datos de la empresa
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012', // Se corrigió el formato
            'telefono' => '6001234560',
            'imagen' => $file,
        ];

        $response = $this->post(route('empresas.store'), $data);

        $response->assertSessionHasErrors(['telefono']);

        // Verificar que no se creó la empresa ni el usuario en la BD
        $this->assertDatabaseMissing('users', ['name' => 'Empresa Test']);
        $this->assertDatabaseMissing('empresas', ['cif' => 'B12345678']);
    }

    public function testStoreEmptyTelefono()
    {
        Storage::fake('public'); // Fake para simular almacenamiento

        $file = UploadedFile::fake()->image('empresa.jpg');

        $data = [
            // Datos del usuario
            'name' => 'Empresa Test',
            'email' => 'empresa@test.com',
            'password' => 'password123', // Se requiere para crear el usuario

            // Datos de la empresa
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012', // Se corrigió el formato
            'telefono' => '',
            'imagen' => $file,
        ];

        $response = $this->post(route('empresas.store'), $data);

        $response->assertSessionHasErrors(['telefono']);

        // Verificar que no se creó la empresa ni el usuario en la BD
        $this->assertDatabaseMissing('users', ['name' => 'Empresa Test']);
        $this->assertDatabaseMissing('empresas', ['cif' => 'B12345678']);
    }

    public function testStoreBadImagen()
    {
        Storage::fake('public'); // Fake para simular almacenamiento

        $file = UploadedFile::fake()->image('empresa.txt');

        $data = [
            // Datos del usuario
            'name' => 'Empresa Test',
            'email' => 'empresa@test.com',
            'password' => 'password123', // Se requiere para crear el usuario

            // Datos de la empresa
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012', // Se corrigió el formato
            'telefono' => '600123456',
            'imagen' => $file,
        ];

        $response = $this->post(route('empresas.store'), $data);

        $response->assertSessionHasErrors(['imagen']);

        // Verificar que no se creó la empresa ni el usuario en la BD
        $this->assertDatabaseMissing('users', ['name' => 'Empresa Test']);
        $this->assertDatabaseMissing('empresas', ['cif' => 'B12345678']);
    }


    public function testUpdate()
    {
        // Recupera una empresa existente desde la base de datos
        $empresa = Empresa::first();

        $data = [
            'name' => 'Nuevo Nombre',
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 124',
            'cuentaBancaria' => 'ES1234567890123456789012',
            'telefono' => '600123456',
            'email' => 'empresa2@test.com'
        ];

        $response = $this->put(route('empresas.update', $empresa->id), $data);

        // Verificar redirección correcta
        $response->assertRedirect(route('empresas.index'))
            ->assertSessionHas('success', 'Empresa actualizada correctamente');

        // Verificar que los datos se guardaron
        $this->assertDatabaseHas('empresas', ['id' => $empresa->id, 'name' => 'Nuevo Nombre']);
    }

    public function testUpdateNotFoundEmpresa()
    {
        $data = [
            'name' => 'Nuevo Nombre',
            'cif' => 'B12345678',
            'direccion' => 'Calle Nueva 456',
            'cuentaBancaria' => 'ES1234567890123456789012',
            'telefono' => '600123456',
            'email' => 'empresa2@test.com'
        ];

        $response = $this->put(route('empresas.update', 99999), $data);

        $response->assertRedirect(route('empresas.index'))
            ->assertSessionHas('error', 'Empresa no encontrada');

        $this->assertDatabaseMissing('empresas', ['name' => 'Nuevo Nombre']);
    }

    public function testUpdateBadName()
    {

        $empresa = Empresa::first();

        $data = [
            'name' => '',
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 124',
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012',
            'telefono' => '600123456',
            'email' => 'empresa2@test.com'
        ];

        $response = $this->put(route('empresas.update', $empresa->id), $data);

        $response->assertSessionHasErrors(['name']);

        $this->assertDatabaseMissing('empresas', ['name' => 'Nuevo Nombre']);
    }

    public function testUpdateBadCif()
    {

        $empresa = Empresa::first();

        $data = [
            'name' => 'Nuevo Nombre',
            'cif' => '012345678',
            'direccion' => 'Calle Falsa 124',
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012',
            'telefono' => '600123456',
            'email' => 'empresa2@test.com'
        ];

        $response = $this->put(route('empresas.update', $empresa->id), $data);

        $response->assertSessionHasErrors(['cif']);

        $this->assertDatabaseMissing('empresas', ['name' => 'Nuevo Nombre']);
    }

    public function testUpdateBadCifLower()
    {

        $empresa = Empresa::first();

        $data = [
            'name' => 'Nuevo Nombre',
            'cif' => 'B1234567',
            'direccion' => 'Calle Falsa 124',
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012',
            'telefono' => '600123456',
            'email' => 'empresa2@test.com'
        ];

        $response = $this->put(route('empresas.update', $empresa->id), $data);

        $response->assertSessionHasErrors(['cif']);

        $this->assertDatabaseMissing('empresas', ['name' => 'Nuevo Nombre']);
    }


    public function testUpdateBadCifUpper()
    {

        $empresa = Empresa::first();

        $data = [
            'name' => 'Nuevo Nombre',
            'cif' => 'B123456789',
            'direccion' => 'Calle Falsa 124',
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012',
            'telefono' => '600123456',
            'email' => 'empresa2@test.com'
        ];

        $response = $this->put(route('empresas.update', $empresa->id), $data);

        $response->assertSessionHasErrors(['cif']);

        $this->assertDatabaseMissing('empresas', ['name' => 'Nuevo Nombre']);
    }

    public function testUpdateEmptyCif()
    {

        $empresa = Empresa::first();

        $data = [
            'name' => 'Nuevo Nombre',
            'cif' => '',
            'direccion' => 'Calle Falsa 124',
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012',
            'telefono' => '600123456',
            'email' => 'empresa2@test.com'
        ];

        $response = $this->put(route('empresas.update', $empresa->id), $data);

        $response->assertSessionHasErrors(['cif']);

        $this->assertDatabaseMissing('empresas', ['name' => 'Nuevo Nombre']);
    }

    public function testUpdateBadCuentaBancaria()
    {

        $empresa = Empresa::first();

        $data = [
            'name' => 'Nuevo Nombre',
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 124',
            'cuentaBancaria' => 'FR1234567890123456789012',
            'telefono' => '600123456',
            'email' => 'empresa2@test.com'
        ];

        $response = $this->put(route('empresas.update', $empresa->id), $data);

        $response->assertSessionHasErrors(['cuentaBancaria']);

        $this->assertDatabaseMissing('empresas', ['name' => 'Nuevo Nombre']);
    }

    public function testUpdateBadCuentaBancariaLower()
    {

        $empresa = Empresa::first();

        $data = [
            'name' => 'Nuevo Nombre',
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 124',
            'cuentaBancaria' => 'ES123456789012345678901',
            'telefono' => '600123456',
            'email' => 'empresa2@test.com'
        ];

        $response = $this->put(route('empresas.update', $empresa->id), $data);

        $response->assertSessionHasErrors(['cuentaBancaria']);

        $this->assertDatabaseMissing('empresas', ['name' => 'Nuevo Nombre']);
    }

    public function testUpdateBadCuentaBancariaUpper()
    {

        $empresa = Empresa::first();

        $data = [
            'name' => 'Nuevo Nombre',
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 124',
            'cuentaBancaria' => 'ES12345678901234567890123',
            'telefono' => '600123456',
            'email' => 'empresa2@test.com'
        ];

        $response = $this->put(route('empresas.update', $empresa->id), $data);

        $response->assertSessionHasErrors(['cuentaBancaria']);

        $this->assertDatabaseMissing('empresas', ['name' => 'Nuevo Nombre']);
    }

    public function testUpdateEmptyCuentaBancaria()
    {

        $empresa = Empresa::first();

        $data = [
            'name' => 'Nuevo Nombre',
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 124',
            'cuentaBancaria' => '',
            'telefono' => '600123456',
            'email' => 'empresa2@test.com'
        ];

        $response = $this->put(route('empresas.update', $empresa->id), $data);

        $response->assertSessionHasErrors(['cuentaBancaria']);

        $this->assertDatabaseMissing('empresas', ['name' => 'Nuevo Nombre']);
    }

    public function testUpdateBadTelefono()
    {

        $empresa = Empresa::first();

        $data = [
            'name' => 'Nuevo Nombre',
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 124',
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012',
            'telefono' => '100123456',
            'email' => 'empresa2@test.com'
        ];

        $response = $this->put(route('empresas.update', $empresa->id), $data);

        $response->assertSessionHasErrors(['telefono']);

        $this->assertDatabaseMissing('empresas', ['name' => 'Nuevo Nombre']);
    }

    public function testUpdateBadTelefonoLower()
    {

        $empresa = Empresa::first();

        $data = [
            'name' => 'Nuevo Nombre',
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 124',
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012',
            'telefono' => '60012345',
            'email' => 'empresa2@test.com'
        ];

        $response = $this->put(route('empresas.update', $empresa->id), $data);

        $response->assertSessionHasErrors(['telefono']);

        $this->assertDatabaseMissing('empresas', ['name' => 'Nuevo Nombre']);
    }

    public function testUpdateBadTelefonoUpper()
    {

        $empresa = Empresa::first();

        $data = [
            'name' => 'Nuevo Nombre',
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 124',
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012',
            'telefono' => '6001234560',
            'email' => 'empresa2@test.com'
        ];

        $response = $this->put(route('empresas.update', $empresa->id), $data);

        $response->assertSessionHasErrors(['telefono']);

        $this->assertDatabaseMissing('empresas', ['name' => 'Nuevo Nombre']);
    }

    public function testUpdateEmptyTelefono()
    {

        $empresa = Empresa::first();

        $data = [
            'name' => 'Nuevo Nombre',
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 124',
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012',
            'telefono' => '',
            'email' => 'empresa2@test.com'
        ];

        $response = $this->put(route('empresas.update', $empresa->id), $data);

        $response->assertSessionHasErrors(['telefono']);

        $this->assertDatabaseMissing('empresas', ['name' => 'Nuevo Nombre']);
    }

    public function testUpdateBadEmail()
    {

        $empresa = Empresa::first();

        $data = [
            'name' => 'Nuevo Nombre',
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 124',
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012',
            'telefono' => '600123456',
            'email' => 'BadEmail'
        ];

        $response = $this->put(route('empresas.update', $empresa->id), $data);

        $response->assertSessionHasErrors(['email']);

        $this->assertDatabaseMissing('empresas', ['name' => 'Nuevo Nombre']);
    }

    public function testUpdateEmptyEmail()
    {

        $empresa = Empresa::first();

        $data = [
            'name' => 'Nuevo Nombre',
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 124',
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012',
            'telefono' => '600123456',
            'email' => ''
        ];

        $response = $this->put(route('empresas.update', $empresa->id), $data);

        $response->assertSessionHasErrors(['email']);

        $this->assertDatabaseMissing('empresas', ['name' => 'Nuevo Nombre']);
    }

    public function testDestroy()
    {
        Storage::fake('public'); // Asegurar almacenamiento de prueba

        $file = UploadedFile::fake()->image('empresa.jpg');

        // 🔹 Crear el usuario
        $user = User::factory()->create([
            'name' => 'Usuario Test',
            'email' => 'empresa@test.com',
            'password' => bcrypt('password123'),
        ]);

        // 🔹 Crear la empresa con usuario_id
        $empresa = Empresa::create([
            'name' => 'Empresa Test',
            'email' => 'empresa@test.com',
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012',
            'telefono' => '600123456',
            'imagen' => $file->store('empresas', 'public'), // Almacenar en public/empresas
            'usuario_id' => $user->id,
            'isDeleted' => false,
        ]);

        // 🔹 Eliminar la empresa
        $response = $this->delete(route('empresas.destroy', $empresa->id));

        $response->assertRedirect(route('empresas.index'))
            ->assertSessionHas('success', 'Empresa eliminada correctamente');

        $this->assertDatabaseMissing('empresas', ['id' => $empresa->id]);

        // 🔹 Verificar que la imagen se eliminó
        Storage::disk('public')->assertMissing('empresas/' . basename($empresa->imagen));
    }

    public function testDestroyNotFoundEmpresa()
    {
        $response = $this->delete(route('empresas.destroy', 99999));

        $response->assertRedirect(route('empresas.index'))
            ->assertSessionHas('error', 'No se ha encontrado la empresa');

        // Asegurar que la tabla sigue intacta y no se eliminó nada
        $this->assertDatabaseCount('empresas', Empresa::count());
    }


}
