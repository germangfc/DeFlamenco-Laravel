<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
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


    public function index()
    {

        $response = $this->get(route('empresas.index'));

        $response->assertStatus(200)
            ->assertViewIs('empresas.index')
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


    public function showNotFound()
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


    public function showByNombreNotFound()
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
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012', // Se corrigió el formato
            'telefono' => '600123456',
            'imagen' => $file,
        ];

        $response = $this->post(route('empresas.store'), $data);

        $response->assertRedirect(route('empresas.index'))
            ->assertSessionHas('status', 'Empresa creada correctamente');

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
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012',
        ]);

        // Verificar que la imagen se ha guardado correctamente en la carpeta 'public/empresas'
        Storage::disk('public')->assertExists('empresas/' . $file->hashName());
    }

    public function testUpdate()
    {
        // Recupera una empresa existente desde la base de datos
        $empresa = Empresa::first();

        $data = [
            'name' => 'Nuevo Nombre',
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 124',
            'cuentaBancaria' => 'ES12 3456 7890 1234 5678 9012',
            'telefono' => '600123456',
            'email' => 'empresa2@test.com'
        ];

        $response = $this->put(route('empresas.update', $empresa->id), $data);

        // Verificar redirección correcta
        $response->assertRedirect(route('empresas.index'))
            ->assertSessionHas('status', 'Empresa actualizada correctamente');

        // Verificar que los datos se guardaron
        $this->assertDatabaseHas('empresas', ['id' => $empresa->id, 'name' => 'Nuevo Nombre']);
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
            ->assertSessionHas('status', 'Empresa eliminada correctamente');

        $this->assertDatabaseMissing('empresas', ['id' => $empresa->id]);

        // 🔹 Verificar que la imagen se eliminó
        Storage::disk('public')->assertMissing('empresas/' . basename($empresa->imagen));
    }


}
