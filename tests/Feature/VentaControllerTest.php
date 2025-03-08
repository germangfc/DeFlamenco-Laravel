<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Tests\Fakes\VentaFake;
use Tests\TestCase;
use App\Models\Venta;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VentaControllerTest extends TestCase
{
    use RefreshDatabase;

    protected VentaFake $ventaDummy;
    protected $dummyColVentas;


    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);

        // Crear un usuario con rol admin
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('admin');

        $this->noAdminUser = User::factory()->create();
        $this->noAdminUser->assignRole('cliente');

        // Configurar caché
        Config::set('cache.default', 'array');

        $this->ventaDummy = new VentaFake([
            'id'          => 1,
            'created_at'  => now(),
            'lineasVenta' => [['1', '100', 'Evento 1', '01-10-2025', '18:00:00', 'Madrid']],
            'guid'        => 'guid-123'
        ]);

        $this->dummyColVentas = collect([
            new VentaFake([
                'id'          => 1,
                'created_at'  => now(),
                'lineasVenta' => [['1', '100', 'Evento 1', '01-10-2025', '18:00:00', 'Madrid']],
                'guid'        => 'guid-123'
            ]),
            new VentaFake([
                'id'          => 2,
                'created_at'  => now()->subDay(),
                'lineasVenta' => [['2', '110', 'Evento 2', '12-04-2025', '19:00:00', 'Madrid']],
                'guid'        => 'guid-456'
            ]),
        ]);

    }

    public function testShowRetrievesVentaFromCache()
    {

        // Autenticar el usuario
        $this->actingAs($this->adminUser);

        // Configurar el driver de caché a "array"
        Config::set('cache.default', 'array');

        // Simular que la venta ya está en la caché
        Cache::put('venta_1', $this->ventaDummy, 60);

        // Realizar la solicitud al controlador
        $response = $this->get(route('ventas.show', ['id' => 1]));

        // Verificar que la respuesta es correcta
        $response->assertStatus(200)
            ->assertViewIs('ventas.show')
            ->assertViewHas('venta', fn($v) => $v->guid === 'guid-123');

        // Verificar que la caché no se haya sobrescrito innecesariamente
        $this->assertEquals($this->ventaDummy, Cache::get('venta_1'));
    }


    


    /*public function testShowRetrievesVentaFromCacheOK()
    {

        // Crear un usuario con el permiso 'admin'
        $user = User::factory()->create();
        $user->givePermissionTo('admin'); // Asegúrate de usar Spatie Permission o similar

        // Autenticar el usuario
        $this->actingAs($user);

        // Configurar el driver de caché a "array"
        Config::set('cache.default', 'array');

        // Crear una venta dummy
        $ventaDummy = new VentaFake([
            'id'          => 1,
            'created_at'  => now(),
            'lineasVenta' => [['1', '100', 'Evento 1', '01-10-2025', '18:00:00', 'Madrid']],
            'guid'        => 'guid-123'
        ]);

        // Simular que la venta ya está en la caché
        Cache::put('venta_1', $ventaDummy, 60);

        // Realizar la solicitud al controlador
        $response = $this->get(route('ventas.show', ['id' => 1]));

        // Verificar que la respuesta es correcta
        $response->assertStatus(200)
            ->assertViewIs('ventas.show')
            ->assertViewHas('venta', fn($v) => $v->guid === 'guid-123');

        // Verificar que la caché no se haya sobrescrito innecesariamente
        $this->assertEquals($ventaDummy, Cache::get('venta_1'));
    }*/
    /*public function testIndexReturnsVentasView()
    {
        $dummyVentas = collect([
            new Venta([
                'id'          => 1,
                'created_at'  => now(),
                'lineasVenta' => [['1', '100', 'Evento 1', '01-10-2025', '18:00:00', 'Madrid']],
                'guid'        => 'guid-123'
            ]),
            new Venta([
                'id'          => 2,
                'created_at'  => now()->subDay(),
                'lineasVenta' => [['2', '110', 'Evento 2', '12-04-2025', '19:00:00', 'Madrid']],
                'guid'        => 'guid-456'
            ]),
        ]);

        Venta::shouldReceive('orderBy->paginate')
            ->with('created_at', 'DESC', 5)
            ->once()
            ->andReturn($dummyVentas);

        $response = $this->get(route('ventas.index'));

        $response->assertViewIs('ventas.index');
        $response->assertViewHas('ventas', $dummyVentas);
    }*/




}
