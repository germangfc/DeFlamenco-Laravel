<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\VentaFake;
use Tests\TestCase;
use App\Models\Venta;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VentaControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $noAdminUser;

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

        // Crear algunas ventas para pruebas
        Venta::factory()->count(7)->create();
    }

    #[Test]
    public function testIndexDisplaysPaginatedVentas()
    {
        $this->actingAs($this->adminUser);

        $ventasOrdenadas = Venta::orderByDesc('_id')->paginate(5);

        $response = $this->get(route('ventas.index'));

        $response->assertStatus(200)
            ->assertViewIs('ventas.index')
            ->assertViewHas('ventas', function ($ventas) use ($ventasOrdenadas) {
                $actualIds = collect($ventas->items())->pluck('id')->toArray();
                $expectedIds = $ventasOrdenadas->pluck('id')->toArray();
                return $actualIds === $expectedIds;
            });
    }


    #[Test]
    public function testIndexHandlesEmptyVentas()
    {
        $this->actingAs($this->adminUser);

        Venta::truncate();

        $paginatedVentas = Venta::paginate(5);

        $response = $this->get(route('ventas.index'));

        $response->assertStatus(200)
            ->assertViewIs('ventas.index')
            ->assertViewHas('ventas', fn($ventas) => $ventas->isEmpty());
    }

    #[Test]
    public function testIndexReturnsForbiddenForNonAdminUser()
    {
        $this->actingAs($this->noAdminUser);

        $response = $this->get(route('ventas.index'));

        $response->assertStatus(403)->assertSee('This action is unauthorized.');
    }

    #[Test]
    public function testShowRetrievesVentaFromCache()
    {
        $this->actingAs($this->adminUser);

        $venta = Venta::factory()->create();

        Cache::put("venta_{$venta->id}", $venta, 60);

        $response = $this->get(route('ventas.show', ['id' => $venta->id]));

        $response->assertStatus(200)
            ->assertViewIs('ventas.show')
            ->assertViewHas('venta', fn($v) => $v->guid === $venta->guid);

        $this->assertEquals($venta, Cache::get("venta_{$venta->id}"));
    }

    #[Test]
    public function testShowStoresVentaInCacheIfNotPresent()
    {
        $this->actingAs($this->adminUser);

        Cache::forget('venta_1');

        $ventaFromDb = Venta::factory()->create();

        $response = $this->get(route('ventas.show', ['id' => $ventaFromDb->id]));

        $response->assertStatus(200)
            ->assertViewIs('ventas.show')
            ->assertViewHas('venta', fn($v) => $v->guid === $ventaFromDb->guid);

        // Obtener la venta desde la caché y convertir las fechas a un formato común
        $cachedVenta = Cache::get("venta_{$ventaFromDb->id}");

        $this->assertEquals($ventaFromDb->only(['id', 'guid', 'created_at', 'updated_at']),
            $cachedVenta->only(['id', 'guid', 'created_at', 'updated_at']));

        $this->assertEquals($ventaFromDb->created_at->toDateTimeString(), $cachedVenta->created_at->toDateTimeString());
        $this->assertEquals($ventaFromDb->updated_at->toDateTimeString(), $cachedVenta->updated_at->toDateTimeString());
    }

    #[Test]
    public function testShowRedirectsIfVentaNotFound()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('ventas.show', ['id' => 123]));

        $response->assertRedirect(route('ventas.index'))
            ->assertSessionHas('error', 'Venta no encontrada');
    }
}
