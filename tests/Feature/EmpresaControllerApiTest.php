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
use Tymon\JWTAuth\Facades\JWTAuth;

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
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);

        $response = $this->getJson('/api/empresas', [
            'Authorization' => 'Bearer ' . $token
        ]);

        // Verificar el estado de la respuesta y los datos
        $response->assertStatus(200)
            ->assertJsonPath('data', fn ($data) => count($data) === 5);
    }

    public function test_empresa_en_cache()
    {

        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $empresa = Empresa::factory()->create();

        Cache::shouldReceive('get')
            ->once()
            ->with("empresa_{$empresa->id}")
            ->andReturn($empresa);

        $response = $this->getJson("/api/empresas/{$empresa->id}", [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $empresa->id,
                'name' => $empresa->name,
            ]);
    }
    public function test_empresa_no_en_cache()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
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

        $response = $this->getJson("/api/empresas/{$empresa->id}", [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $empresa->id,
                'name' => $empresa->name,
            ]);
    }


    #[Test]
    public function test_puede_obtener_una_empresa_por_id()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $empresa = Empresa::first();

        $this->assertNotNull($empresa, 'No se encontró ninguna empresa en la base de datos.');

        $response = $this->getJson("/api/empresas/{$empresa->id}", [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $empresa->id,
                'name' => $empresa->name,
            ]);
    }


    public function test_empresa_not_found()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        Cache::shouldReceive('get')
            ->once()
            ->with("empresa_999")
            ->andReturnNull();


        $response = $this->getJson("/api/empresas/999", [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Empresa no encontrada',
            ]);
    }

    #[Test]
    public function test_get_by_nombre_en_cache(){
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $empresa = Empresa::factory()->create();

        Cache::shouldReceive('get')
            ->once()
            ->with("empresa_nombre_{$empresa->name}")
            ->andReturn($empresa);

        $response = $this->getJson("/api/empresas/nombre/{$empresa->name}", [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $empresa->id,
                'name' => $empresa->name,
            ]);
    }

    #[Test]
    public function test_get_by_nombre_no_en_cache(){
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $empresa = Empresa::factory()->create();

        Cache::shouldReceive('get')
            ->once()
            ->with("empresa_nombre_{$empresa->name}")
            ->andReturnNull();


        Cache::shouldReceive('put')
            ->once()
            ->with("empresa_nombre_{$empresa->name}", Mockery::on(function ($arg) use ($empresa) {
                return $arg instanceof Empresa && $arg->name === $empresa->name;
            }), 20);


        $response = $this->getJson("/api/empresas/nombre/{$empresa->name}", [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $empresa->id,
                'name' => $empresa->name,
            ]);
    }

    public function test_get_by_name_not_found()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        Cache::shouldReceive('get')
            ->once()
            ->with('empresa_nombre_lalala')
            ->andReturn(null);

        $response = $this->getJson("/api/empresas/nombre/lalala", [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Empresa no encontrada',
            ]);
    }

    #[Test]
    public function test_get_by_cif_en_cache(){
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $empresa = Empresa::factory()->create();

        Cache::shouldReceive('get')
            ->once()
            ->with("empresa_cif_{$empresa->cif}")
            ->andReturn($empresa);

        $response = $this->getJson("/api/empresas/cif/{$empresa->cif}", [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $empresa->id,
                'name' => $empresa->name,
            ]);
    }

    #[Test]
    public function test_get_by_cif_no_en_cache(){
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $empresa = Empresa::factory()->create();

        Cache::shouldReceive('get')
            ->once()
            ->with("empresa_cif_{$empresa->cif}")
            ->andReturnNull();


        Cache::shouldReceive('put')
            ->once()
            ->with("empresa_cif_{$empresa->cif}", Mockery::on(function ($arg) use ($empresa) {
                return $arg instanceof Empresa && $arg->cif === $empresa->cif;
            }), 20);


        $response = $this->getJson("/api/empresas/cif/{$empresa->cif}", [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $empresa->id,
                'name' => $empresa->name,
            ]);
    }

    public function test_get_by_cif_not_found()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        Cache::shouldReceive('get')
            ->once()
            ->with('empresa_cif_lalala')
            ->andReturn(null);

        $response = $this->getJson("/api/empresas/cif/lalala", [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Empresa no encontrada',
            ]);
    }



    #[Test]
    public function test_puede_crear_una_empresa()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $user = User::factory()->create();

        $empresaData = Empresa::factory()->make()->toArray();
        $empresaData['email'] ='juanMA@example.com';
        $empresaData['password'] = 'locoDelPueblo';
        $empresaData['name'] = 'Empresa Ejemplo S.L.';
        $empresaData['direccion'] = 'Calle Falsa 123, Madrid';
        $empresaData['cif'] = 'B1234567J';
        $empresaData['cuentaBancaria'] = 'ES12 34567890123456789012';
        $empresaData['telefono'] = '669843935';

        $response = $this->postJson('/api/empresas', $empresaData, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['message' => 'Empresa creada']);

        $this->assertDatabaseHas('empresas', ['email' => $empresaData['email']]);
    }


    #[Test]
    public function test_no_puede_crear_una_empresa_con_datos_invalidos()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $response = $this->postJson('/api/empresas', [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cif', 'name', 'direccion', 'cuentaBancaria', 'telefono', 'email', 'password']);
    }

    #[Test]
    public function test_no_puede_crear_una_empresa_cif_erroneo(){
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $response = $this->postJson('/api/empresas', [
            'cif' => 'B1234567890',
            'name' => 'Empresa Test',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 34567890123456789012',
            'telefono' => '669843935',
            'email' => 'juanMA@example.com',
            'password' => 'password123',
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cif']);
    }

    #[Test]
    public function test_no_puede_crear_una_empresa_nombre_no_Unico(){
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $empresa = Empresa::factory()->create(['name' => 'Empresa Test']);
        $nuevaData = ['name' => 'Empresa Test'];

        $response = $this->postJson('/api/empresas', $nuevaData, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    #[Test]
    public function test_no_puede_crear_una_empresa_cuenta_incorrecta(){
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $response = $this->postJson('/api/empresas', [
            'cif' => 'B1234567J',
            'name' => 'Empresa Test',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12345678901234567890',
            'telefono' => '669843935',
            'email' => 'juanMA@example.com',
            'password' => 'password123',
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cuentaBancaria']);
    }

    #[Test]
    public function test_no_puede_crear_una_empresa_telefono_incorrecto(){
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $response = $this->postJson('/api/empresas', [
            'cif' => 'B1234567J',
            'name' => 'Empresa Test',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 34567890123456789012',
            'telefono' => '+469876543',
            'email' => 'juanMA@example.com',
            'password' => 'password123',
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['telefono']);
    }

    #[Test]
    public function test_no_puede_crear_una_empresa_email_incorrecto(){
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $response = $this->postJson('/api/empresas', [
            'cif' => 'B1234567J',
            'name' => 'Empresa Test',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 34567890123456789012',
            'telefono' => '669843935',
            'email' => 'juanmaexample.com',
            'password' => 'password123',
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function test_no_puede_crear_una_empresa_email_existente(){
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        Empresa::factory()->create(['email' => 'juanMA@example.com']);

        $response = $this->postJson('/api/empresas', [
            'cif' => 'B1234567J',
            'name' => 'Empresa Test',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 34567890123456789012',
            'telefono' => '669843935',
            'email' => 'juanMA@example.com',
            'password' => 'password123',
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);


        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function test_puede_actualizar_una_empresa()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        Cache::spy(); // Espiar la caché para verificar interacciones

        // Crear usuario y empresa con valores fijos
        $user = User::factory()->create();
        $empresa = Empresa::factory()->create([
            'usuario_id' => $user->id,
            'cif' => 'B1234567J', // Asegurar un valor fijo para la caché
        ]);

        $nuevaData = ['name' => 'Empresa Actualizada'];

        $response = $this->putJson("/api/empresas/{$empresa->id}", $nuevaData, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('empresas', ['id' => $empresa->id, 'name' => 'Empresa Actualizada']);

        // Verificar que las claves de la caché fueron eliminadas
        Cache::shouldHaveReceived('forget')->with("empresa_{$empresa->id}")->once();
        Cache::shouldHaveReceived('forget')->with("empresa_cif_{$empresa->cif}")->once();
        Cache::shouldHaveReceived('forget')->with("user_{$user->id}")->once();

        // Verificar que las nuevas claves fueron guardadas en la caché
        Cache::shouldHaveReceived('put')->with("empresa_{$empresa->id}", \Mockery::type(Empresa::class), 20)->once();
        Cache::shouldHaveReceived('put')->with("empresa_cif_{$empresa->cif}", \Mockery::type(Empresa::class), 20)->once();
        Cache::shouldHaveReceived('put')->with("user_{$user->id}", \Mockery::type(User::class), 20)->once();
    }


    #[Test]
    public function test_no_puede_actualizar_una_empresa_not_found(){
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $response = $this->putJson('/api/empresas/999', ['name' => 'Empresa Actualizada'], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(404);
    }

    #[Test]
    public function test_no_puede_actualizar_una_empresa_con_nombre_no_unico(){
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $empresa = Empresa::factory()->create(['name' => 'Empresa Test']);
        $nuevaData = [
            'cif' => 'B1234567J',
            'name' => 'Empresa Test',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 34567890123456789012',
            'telefono' => '+34698765432',
            'email' => 'juanMA@example.com',
            'password' => 'password123'
            ];

        $response = $this->putJson("/api/empresas/{$empresa->id}", $nuevaData, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    #[Test]
    public function test_no_puede_actualizar_cuenta_invalida()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $empresa = Empresa::factory()->create();
        $nuevaData = [
            'cif' => 'B1234567J',
            'name' => 'Empresa Test',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12345678901234567890',
            'telefono' => '+34698765432',
            'email' => 'juanMA@example.com',
            'password' => 'password123'
        ];

        $response = $this->putJson("/api/empresas/{$empresa->id}", $nuevaData, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cuentaBancaria']);
    }

    #[Test]
    public function test_no_puede_actualizar_cif_invalido(){
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $empresa = Empresa::factory()->create();
        $nuevaData = [
            'cif' => "ajajaja",
            'name' => 'Empresa Test',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 34567890123456789012',
            'telefono' => '+34698765432',
            'email' => 'juanMA@example.com',
            'password' => 'password123'];

        $response = $this->putJson("/api/empresas/{$empresa->id}", $nuevaData, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cif']);
    }

    #[Test]
    public function test_no_puede_actualizar_telefono_invalido(){
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $empresa = Empresa::factory()->create();
        $nuevaData = ['cif' => 'B1234567J',
            'name' => 'Empresa Test',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 34567890123456789012',
            'telefono' => '+469876543',
            'email' => 'juanMA@example.com',
            'password' => 'password123'];

        $response = $this->putJson("/api/empresas/{$empresa->id}", $nuevaData, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['telefono']);
    }

    #[Test]
    public function test_no_puede_actualizar_email_invalido(){
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        $empresa = Empresa::factory()->create();
        $nuevaData = ['cif' => 'B1234567J',
            'name' => 'Empresa Test',
            'direccion' => 'Calle Falsa 123',
            'cuentaBancaria' => 'ES12 34567890123456789012',
            'telefono' => '+34669843935',
            'email' => 'juanmaexample.com',
            'password' => 'password123'];

        $response = $this->putJson("/api/empresas/{$empresa->id}", $nuevaData, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function test_no_puede_eliminar_una_empresa_not_found()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        Cache::spy();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->deleteJson('/api/empresas/999');


        $response->assertStatus(404);

        Cache::shouldNotHaveReceived('forget');
    }

    #[Test]
    public function test_puede_eliminar_una_empresa()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $token = JWTAuth::fromUser($admin);
        Cache::spy();
        dump($admin);

        $empresa = Empresa::factory()->create();

        Cache::put("empresa_{$empresa->id}", $empresa, 20);
        Cache::put("user_{$empresa->usuario_id}",$empresa, 20);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->deleteJson("/api/empresas/{$empresa->id}");



        $response->assertStatus(204);

        $this->assertDatabaseHas('empresas', ['id' => $empresa->id, 'isDeleted' => true]);
        $this->assertDatabaseHas('users', ['id' => $empresa->usuario_id, 'isDeleted' => true]);

        Cache::shouldHaveReceived('forget')->with("empresa_{$empresa->id}");
        Cache::shouldHaveReceived('forget')->with("user_{$empresa->usuario_id}");
    }
}
