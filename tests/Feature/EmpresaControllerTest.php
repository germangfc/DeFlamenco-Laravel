<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmpresaControllerTest extends TestCase
{
    use RefreshDatabase; // Reinicia la base de datos en cada test

    protected function setUp(): void
    {
        parent::setUp(); // Inicializa Laravel

        // Aquí puedes usar Eloquent
        User::factory()->create(['id' => 1]);
        User::factory()->create(['id' => 2]);
    }

    /** @test */
    public function index()
    {

        Artisan::call('db:seed', ['--class' => 'EmpresasTableSeeder']); // Ejecuta solo EmpresasTableSeeder

        $response = $this->get(route('empresas.index'));

        $response->assertStatus(200)
            ->assertViewIs('empresas.index')
            ->assertViewHas('empresas');
    }



    public function testShow()
    {

        // Ejecuta el seeder para cargar las empresas
        Artisan::call('db:seed', ['--class' => 'EmpresasTableSeeder']);

        // Recupera una empresa existente desde la base de datos
        $empresa = Empresa::first();

        $response = $this->get(route('empresas.show', $empresa->id));

        $response->assertStatus(200)
            ->assertViewIs('empresas.show')
            ->assertViewHas('empresa', $empresa);
    }

    /** @test */
    public function showNotFound()
    {
        $response = $this->get(route('empresas.show', 999));

        $response->assertRedirect(route('empresas.index'))
            ->assertSessionHas('error', 'Empresa no encontrada');
    }


    public function testShowByCif()
    {

        // Ejecuta el seeder para cargar las empresas
        Artisan::call('db:seed', ['--class' => 'EmpresasTableSeeder']);

        // Recupera la empresa por su CIF
        $empresa = Empresa::where('cif', 'B12345678')->first();

        $response = $this->get(route('empresas.showByCif', 'B12345678'));

        $response->assertRedirect(route('empresas.show', $empresa));
    }

    public function testShowByCifNotFound()
    {

        $response = $this->get(route('empresas.showByCif', 'B12345679'));

        $response->assertRedirect(route('empresas.index'))
            ->assertSessionHas('error', 'Empresa no encontrada');
    }

    public function testShowByNombre()
    {

        // Ejecuta el seeder para cargar las empresas
        Artisan::call('db:seed', ['--class' => 'EmpresasTableSeeder']);

        // Recupera la empresa por su nombre
        $empresa = Empresa::where('nombre', 'Empresa Ejemplo 1')->first();

        $response = $this->get(route('empresas.showByNombre', 'Empresa Ejemplo 1'));

        $response->assertRedirect(route('empresas.show', $empresa));
    }

    /** @test */
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

        $user = User::factory()->create(['id' => 3]);

        $data = [
            'usuario_id' => $user->id,
            'cif' => 'B12345678',
            'nombre' => 'Empresa Test',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES1234567890123456789012',
            'telefono' => '600123456',
            'email' => 'empresa@test.com',
            'imagen' => $file,
            'isDeleted' => false
        ];

        $response = $this->post(route('empresas.store'), $data);

        $response->assertRedirect(route('empresas.index'))
            ->assertSessionHas('status', 'Empresa creada correctamente');

        // Verificar que la empresa se ha guardado en la BD
        $this->assertDatabaseHas('empresas', [
            'usuario_id' => $user->id,
            'nombre' => 'Empresa Test',
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 123',
            'telefono' => '600123456',
            'email' => 'empresa@test.com',
            'cuentaBancaria' => 'ES1234567890123456789012'
        ]);

        // Verificar que la imagen se ha guardado correctamente en la carpeta 'public/empresas'
        Storage::disk('public')->assertExists('empresas/' . $file->hashName());
    }


    public function testUpdate()
    {

        // Ejecuta el seeder para cargar las empresas
        Artisan::call('db:seed', ['--class' => 'EmpresasTableSeeder']);

        // Recupera una empresa existente desde la base de datos
        $empresa = Empresa::first();

        $data = [
            'nombre' => 'Nuevo Nombre',
            'cif' => 'B12345678',
            'direccion' => 'Calle Falsa 124',
            'cuentaBancaria' => 'ES1234567890123456789012',
            'telefono' => '600123456',
            'email' => 'empresa@test.com'];

        $response = $this->put(route('empresas.update', $empresa->id), $data);

        $response->assertRedirect(route('empresas.index'))
            ->assertSessionHas('status', 'Empresa actualizada correctamente');

        $this->assertDatabaseHas('empresas', ['id' => $empresa->id, 'nombre' => 'Nuevo Nombre']);
    }

    public function testDestroy()
    {

        Storage::fake('public');

        // Ejecuta el seeder para cargar las empresas
        Artisan::call('db:seed', ['--class' => 'EmpresasTableSeeder']);

        $user = User::factory()->create(['id' => 3]);

        $empresa = Empresa::create([
            'usuario_id' => $user->id,
            'cif' => 'B12345678',
            'nombre' => 'Empresa para Eliminar',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES1234567890123456789012',
            'telefono' => '600123456',
            'email' => 'empresa@eliminar.com',
            'imagen' => 'storage/empresa.jpg',
            'isDeleted' => false
        ]);

        Storage::put('storage/empresa.jpg', 'fake-content');

        $response = $this->delete(route('empresas.destroy', $empresa->id));

        $response->assertRedirect(route('empresas.index'))
            ->assertSessionHas('status', 'Empresa eliminada correctamente');

        $this->assertDatabaseMissing('empresas', ['id' => $empresa->id]);
        Storage::assertMissing('storage/empresa.jpg');
    }

}
