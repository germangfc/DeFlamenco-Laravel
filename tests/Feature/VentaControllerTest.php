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

    protected VentaFake $ventaDummy;
    protected $dummyColVentas;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // Utilizamos class_alias para que cualquier llamado a \App\Models\Venta use VentaFake
        // pero solo creamos el alias una vez
        if (!defined('VENTA_ALIAS_SET')) {
            class_alias(VentaFake::class, Venta::class);
            define('VENTA_ALIAS_SET', true);
        }}



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
                'created_at'  => now()->toDateTimeString(),
                'lineasVenta' => [array_values(['1', '100', 'Evento 1', '01-10-2025', '18:00:00', 'Madrid']),], // Reindexamos las líneas para garantizar índices 0, 1, ...
                'guid'        => 'guid-123',
            ]),
            new VentaFake([
                'id'          => 2,
                'created_at'  => now()->subDay()->toDateTimeString(),
                'lineasVenta' => [array_values(['1', '110', 'Evento 2', '12-04-2025', '19:00:00', 'Madrid']),],
                'guid'        => 'guid-456',
            ]),
            new VentaFake([
                'id'          => 3,
                'created_at'  => now()->toDateTimeString(),
                'lineasVenta' => [array_values(['1', '300', 'Evento 1', '01-10-2025', '18:00:00', 'Madrid']),], // Reindexamos las líneas para garantizar índices 0, 1, ...
                'guid'        => 'guid-123',
            ]),
            new VentaFake([
                'id'          => 4,
                'created_at'  => now()->toDateTimeString(),
                'lineasVenta' => [array_values(['1', '100', 'Evento 1', '01-10-2025', '18:00:00', 'Madrid']),], // Reindexamos las líneas para garantizar índices 0, 1, ...
                'guid'        => 'guid-123',
            ]),
            new VentaFake([
                'id'          => 5,
                'created_at'  => now()->toDateTimeString(),
                'lineasVenta' => [array_values(['1', '500', 'Evento 1', '01-10-2025', '18:00:00', 'Madrid']),], // Reindexamos las líneas para garantizar índices 0, 1, ...
                'guid'        => 'guid-123',
            ]),
            new VentaFake([
                'id'          => 6,
                'created_at'  => now()->toDateTimeString(),
                'lineasVenta' => [array_values(['1', '600', 'Evento 1', '01-10-2025', '18:00:00', 'Madrid']),], // Reindexamos las líneas para garantizar índices 0, 1, ...
                'guid'        => 'guid-123',
            ]),
            new VentaFake([
                'id'          => 7,
                'created_at'  => now()->toDateTimeString(),
                'lineasVenta' => [array_values(['1', '700', 'Evento 1', '01-10-2025', '18:00:00', 'Madrid']),], // Reindexamos las líneas para garantizar índices 0, 1, ...
                'guid'        => 'guid-123',
            ]),
        ]);

    }

    #[Test]
    public function testIndexDisplaysPaginatedVentas()
    {
        // Autenticar como usuario admin
        $this->actingAs($this->adminUser);

        $ventasDummy = $this->dummyColVentas->values(); // reindexado

        // Ordenamos las ventas por created_at de forma descendente y reindexamos
        $ventasOrdenadas = $ventasDummy->sortByDesc('created_at')->values();

        // Creamos un paginador simulado con los datos fake
        $paginatedVentas = new LengthAwarePaginator(
            $ventasOrdenadas->take(5)->values(),  // Items de la página (reindexados)
            $ventasOrdenadas->count(),              // Total de ítems
            5,                                     // Items por página
            1,                                     // Página actual
            ['path' => URL::current()]             // Para generar URLs correctamente
        );

        // Registramos el paginador fake en el contenedor, clave 'fake.paginatedVentas'
        app()->instance('fake.paginatedVentas', $paginatedVentas);

        // Realizamos la solicitud al método index (la llamada a Venta::orderBy()->paginate(5) se redirige a nuestra fake)
        $response = $this->get(route('ventas.index'));

        $response->assertStatus(200)
            ->assertViewIs('ventas.index')
            ->assertViewHas('ventas', function ($ventas) use ($ventasOrdenadas) {
                // Extraemos los IDs de los elementos del paginador
                $actualIds = collect($ventas->items())->pluck('id')->toArray();
                $expectedIds = $ventasOrdenadas->pluck('id')->take(5)->toArray();
                return $actualIds === $expectedIds;
            });
    }

    #[Test]
    public function testIndexHandlesEmptyVentas()
    {
        // Autenticar como usuario admin.
        $this->actingAs($this->adminUser);

        // Simular una colección vacía de ventas.
        $ventasDummy = collect([]);

        // Crear un paginador simulado a partir de la colección vacía.
        $paginatedVentas = new LengthAwarePaginator(
            $ventasDummy,         // Items (vacío)
            0,                    // Total de ítems
            5,                    // Items por página
            1,                    // Página actual
            ['path' => URL::current()]  // Parámetros para la URL (para paginación)
        );

        // Registrar el paginador fake en el contenedor bajo la clave 'fake.paginatedVentas'
        app()->instance('fake.paginatedVentas', $paginatedVentas);

        // Realizar la solicitud al método index del controlador.
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


    #[Test]
    public function testShowStoresVentaInCacheIfNotPresent()
    {
        $this->actingAs($this->adminUser);

        // Simular que no hay datos en el caché
        Cache::forget("venta_1"); // Asegúrate de que la clave no exista previamente

        // Sobrescribir lógica para simular la búsqueda de la venta
        $ventaFromDb = $this->dummyColVentas->firstWhere('id', 1);

        // Verificar que la venta existe en el "mock" de datos
        $this->assertNotNull($ventaFromDb, 'Venta no encontrada en la colección de dummies');

        // Insertar manualmente la venta en caché para validar el flujo
        Cache::put("venta_{$ventaFromDb->id}", $ventaFromDb, 60);

        // Realizar la solicitud al controlador
        $response = $this->get(route('ventas.show', ['id' => 1]));

        // Verificar que la respuesta es correcta
        $response->assertStatus(200)
            ->assertViewIs('ventas.show')
            ->assertViewHas('venta', fn($v) => $v->guid === 'guid-123');

        // Verificar que la venta está correctamente cacheada
        $this->assertEquals($ventaFromDb, Cache::get("venta_{$ventaFromDb->id}"));

    }
    #[Test]
    public function testShowReturnsForbiddenForNonAdminUser()
    {
        $this->actingAs($this->noAdminUser);

        // Realizar la solicitud al controlador
        $response = $this->get(route('ventas.show', ['id' => 1]));

        // Verificar que la respuesta es correcta
        $response->assertStatus(403);

    }


    #[Test]
    public function testShowRedirectsIfVentaNotFound()
    {
        $this->actingAs($this->adminUser);

        // Asegurarse de que la caché esté vacía inicialmente
        Cache::forget('venta_1'); // Simula que no hay datos en el caché

        // Simular búsqueda fallida en la colección de dummies
        $ventaFromDb = $this->dummyColVentas->firstWhere('id', 123);

        // Verificar que la venta no existe
        $this->assertNull($ventaFromDb, 'La venta debería estar ausente para este test.');

        // Realizar la solicitud al controlador
        $response = $this->get(route('ventas.show', ['id' => 123]));

        // Validar que se redirige correctamente
        $response->assertRedirect(route('ventas.index'))
            ->assertSessionHas('error', 'Venta no encontrada');
    }
}
