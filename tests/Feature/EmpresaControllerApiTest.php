<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\User;
use Database\Seeders\EmpresasTableSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmpresaControllerApiTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeeder::class);

        User::factory()->create();
        User::factory()->create();
        Empresa::factory(5)->create();
    }
    #[Test]
    public function test_puede_obtener_todas_las_empresas()
    {

        $response = $this->getJson('/api/empresas');

        $response->assertStatus(200)
            ->assertJsonPath('data', fn ($data) => count($data) === 5);
    }

    public function test_empresa_en_cache()
    {
        $empresa = Empresa::factory()->create();

        Cache::shouldReceive('get')
            ->once()
            ->with("empresa_{$empresa->id}")
            ->andReturn($empresa);

        $response = $this->getJson("/api/empresas/{$empresa->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $empresa->id,
                'nombre' => $empresa->nombre,
            ]);
    }
    public function test_empresa_no_en_cache()
    {
        $empresa = Empresa::factory()->create();

        Cache::shouldReceive('get')
            ->once()
            ->with("empresa_{$empresa->id}")
            ->andReturnNull();


        Cache::shouldReceive('put')
            ->once()
            ->with("empresa_{$empresa->id}", Mockery::on(function ($arg) use ($empresa) {
                return $arg instanceof Empresa && $arg->id === $empresa->id;
            }), 20);

        $response = $this->getJson("/api/empresas/{$empresa->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $empresa->id,
                'nombre' => $empresa->nombre,
            ]);
    }


    #[Test]
    public function test_puede_obtener_una_empresa_por_id()
    {
        $empresa = Empresa::first();

        $this->assertNotNull($empresa, 'No se encontró ninguna empresa en la base de datos.');

        $response = $this->getJson("/api/empresas/{$empresa->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $empresa->id,
                'nombre' => $empresa->nombre,
            ]);
    }


    public function test_empresa_not_found()
    {
        Cache::shouldReceive('get')
            ->once()
            ->with("empresa_999")
            ->andReturnNull();


        $response = $this->getJson("/api/empresas/999");

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Empresa no encontrada',
            ]);
    }

    #[Test]
    public function test_get_by_nombre_en_cache(){
        $empresa = Empresa::factory()->create();

        Cache::shouldReceive('get')
            ->once()
            ->with("empresa_nombre_{$empresa->nombre}")
            ->andReturn($empresa);

        $response = $this->getJson("/api/empresas/nombre/{$empresa->nombre}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $empresa->id,
                'nombre' => $empresa->nombre,
            ]);
    }

    #[Test]
    public function test_get_by_nombre_no_en_cache(){
        $empresa = Empresa::factory()->create();

        Cache::shouldReceive('get')
            ->once()
            ->with("empresa_nombre_{$empresa->nombre}")
            ->andReturnNull();


        Cache::shouldReceive('put')
            ->once()
            ->with("empresa_nombre_{$empresa->nombre}", Mockery::on(function ($arg) use ($empresa) {
                return $arg instanceof Empresa && $arg->nombre === $empresa->nombre;
            }), 20);


        $response = $this->getJson("/api/empresas/nombre/{$empresa->nombre}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $empresa->id,
                'nombre' => $empresa->nombre,
            ]);
    }

    public function test_get_by_name_not_found()
    {
        Cache::shouldReceive('get')
            ->once()
            ->with('empresa_nombre_lalala')
            ->andReturn(null);

        $response = $this->getJson("/api/empresas/nombre/lalala");

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Empresa no encontrada',
            ]);
    }


    #[Test]
    public function test_puede_crear_una_empresa()
    {
        $user = User::factory()->create();

        $empresaData = Empresa::factory()->make()->toArray();
        $empresaData['email'] ='juanMA@example.com';
        $empresaData['password'] = 'locoDelPueblo';
        $empresaData['nombre'] = 'Empresa Ejemplo S.L.';
        $empresaData['direccion'] = 'Calle Falsa 123, Madrid';
        $empresaData['cif'] = 'B1234567J';
        $empresaData['cuentaBancaria'] = 'ES12 34567890123456789012';
        $empresaData['telefono'] = '669843935';

        $response = $this->postJson('/api/empresas', $empresaData);

        $response->assertStatus(201)
            ->assertJsonFragment(['message' => 'Empresa creada']);

        $this->assertDatabaseHas('empresas', ['email' => $empresaData['email']]);
    }


    #[Test]
    public function test_no_puede_crear_una_empresa_con_datos_invalidos()
    {
        $response = $this->postJson('/api/empresas', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cif', 'nombre', 'direccion', 'cuentaBancaria', 'telefono', 'email', 'password']);
    }

    #[Test]
    public function test_no_puede_crear_una_empresa_cif_erroneo(){
        $response = $this->postJson('/api/empresas', [
            'cif' => 'B1234567890',
            'nombre' => 'Empresa Test',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 34567890123456789012',
            'telefono' => '669843935',
            'email' => 'juanMA@example.com',
            'password' => 'password123',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cif']);
    }

    #[Test]
    public function test_no_puede_crear_una_empresa_nombre_no_Unico(){
        $empresa = Empresa::factory()->create(['nombre' => 'Empresa Test']);
        $nuevaData = ['nombre' => 'Empresa Test'];

        $response = $this->postJson('/api/empresas', $nuevaData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nombre']);
    }

    #[Test]
    public function test_no_puede_crear_una_empresa_cuenta_incorrecta(){
        $response = $this->postJson('/api/empresas', [
            'cif' => 'B1234567J',
            'nombre' => 'Empresa Test',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12345678901234567890',
            'telefono' => '669843935',
            'email' => 'juanMA@example.com',
            'password' => 'password123',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cuentaBancaria']);
    }

    #[Test]
    public function test_no_puede_crear_una_empresa_telefono_incorrecto(){
        $response = $this->postJson('/api/empresas', [
            'cif' => 'B1234567J',
            'nombre' => 'Empresa Test',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 34567890123456789012',
            'telefono' => '+469876543',
            'email' => 'juanMA@example.com',
            'password' => 'password123',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['telefono']);
    }

    #[Test]
    public function test_no_puede_crear_una_empresa_email_incorrecto(){
        $response = $this->postJson('/api/empresas', [
            'cif' => 'B1234567J',
            'nombre' => 'Empresa Test',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 34567890123456789012',
            'telefono' => '669843935',
            'email' => 'juanmaexample.com',
            'password' => 'password123',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function test_no_puede_crear_una_empresa_email_existente(){
        Empresa::factory()->create(['email' => 'juanMA@example.com']);

        $response = $this->postJson('/api/empresas', [
            'cif' => 'B1234567J',
            'nombre' => 'Empresa Test',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 34567890123456789012',
            'telefono' => '669843935',
            'email' => 'juanMA@example.com',
            'password' => 'password123',
        ]);


        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function test_puede_actualizar_una_empresa()
    {
        $empresa = Empresa::factory()->create();
        $nuevaData = ['nombre' => 'Empresa Actualizada'];

        $response = $this->putJson("/api/empresas/{$empresa->id}", $nuevaData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('empresas', ['id' => $empresa->id, 'nombre' => 'Empresa Actualizada']);
    }

    #[Test]
    public function test_no_puede_actualizar_una_empresa_not_found(){
        $response = $this->putJson('/api/empresas/999', ['nombre' => 'Empresa Actualizada']);

        $response->assertStatus(404);
    }

    #[Test]
    public function test_no_puede_actualizar_una_empresa_con_nombre_no_unico(){
        $empresa = Empresa::factory()->create(['nombre' => 'Empresa Test']);
        $nuevaData = [
            'cif' => 'B1234567J',
            'nombre' => 'Empresa Test',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 34567890123456789012',
            'telefono' => '+34698765432',
            'email' => 'juanMA@example.com',
            'password' => 'password123'
            ];

        $response = $this->putJson("/api/empresas/{$empresa->id}", $nuevaData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nombre']);
    }

    #[Test]
    public function test_no_puede_actualizar_cuenta_invalida()
    {
        $empresa = Empresa::factory()->create();
        $nuevaData = [
            'cif' => 'B1234567J',
            'nombre' => 'Empresa Test',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12345678901234567890',
            'telefono' => '+34698765432',
            'email' => 'juanMA@example.com',
            'password' => 'password123'
        ];

        $response = $this->putJson("/api/empresas/{$empresa->id}", $nuevaData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cuentaBancaria']);
    }

    #[Test]
    public function test_no_puede_actualizar_cif_invalido(){
        $empresa = Empresa::factory()->create();
        $nuevaData = [
            'cif' => "ajajaja",
            'nombre' => 'Empresa Test',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 34567890123456789012',
            'telefono' => '+34698765432',
            'email' => 'juanMA@example.com',
            'password' => 'password123'];

        $response = $this->putJson("/api/empresas/{$empresa->id}", $nuevaData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cif']);
    }

    #[Test]
    public function test_no_puede_actualizar_telefono_invalido(){
        $empresa = Empresa::factory()->create();
        $nuevaData = ['cif' => 'B1234567J',
            'nombre' => 'Empresa Test',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 34567890123456789012',
            'telefono' => '+469876543',
            'email' => 'juanMA@example.com',
            'password' => 'password123'];

        $response = $this->putJson("/api/empresas/{$empresa->id}", $nuevaData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['telefono']);
    }

    #[Test]
    public function test_no_puede_actualizar_email_invalido(){
        $empresa = Empresa::factory()->create();
        $nuevaData = ['cif' => 'B1234567J',
            'nombre' => 'Empresa Test',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 34567890123456789012',
            'telefono' => '+34669843935',
            'email' => 'juanmaexample.com',
            'password' => 'password123'];

        $response = $this->putJson("/api/empresas/{$empresa->id}", $nuevaData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function test_no_puede_eliminar_una_empresa_not_found(){
        $response = $this->deleteJson('/api/empresas/999');

        $response->assertStatus(404);
    }

    #[Test]
    public function test_puede_eliminar_una_empresa()
    {
        $empresa = Empresa::factory()->create();

        $response = $this->deleteJson("/api/empresas/{$empresa->id}");

        $response->assertStatus(204);
        $this->assertDatabaseHas('empresas', ['id' => $empresa->id, 'isDeleted' => true]);
    }
}
