<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmpresaControllerApiTest extends TestCase
{

    use RefreshDatabase;

    #[Test]
    public function test_puede_obtener_todas_las_empresas()
    {
        Empresa::factory()->count(3)->create();

        $response = $this->getJson('/api/empresas');

        $response->assertStatus(200)
            ->assertJsonPath('data', fn ($data) => count($data) === 3);
    }


    #[Test]
    public function test_puede_obtener_una_empresa_por_id()
    {
        $empresa = Empresa::factory()->create();

        $response = $this->getJson("/api/empresas/{$empresa->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $empresa->id,
                'nombre' => $empresa->nombre,
            ]);
    }

    #[Test]
    public function test_devuelve_404_si_la_empresa_no_existe()
    {
        $response = $this->getJson('/api/empresas/999');

        $response->assertStatus(404);
    }

    #[Test]
    public function test_puede_crear_una_empresa()
    {
        $user = User::factory()->create(); // ✅ Crear usuario primero

        $empresaData = Empresa::factory()->make()->toArray();
        $empresaData['email'] ='juanMA@example.com'; // ✅ Email aleatorio
        $empresaData['password'] = 'HIjodePutaMaricon';
        $empresaData['nombre'] = 'Empresa Ejemplo S.L.';
        $empresaData['direccion'] = 'Calle Falsa 123, Madrid';
        $empresaData['cif'] = 'B1234567J';
        $empresaData['cuentaBancaria'] = 'ES12 34567890123456789012';
        $empresaData['telefono'] = '+34698765432'; // ✅ Prefijo +34 agregado

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
            'cif' => 'B1234567890',  // Cif erróneo
            'nombre' => 'Empresa Test',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 34567890123456789012',
            'telefono' => '+34698765432',
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
            'telefono' => '+34698765432',
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
            'telefono' => '+34698765432',
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
            'telefono' => '+34698765432',
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
