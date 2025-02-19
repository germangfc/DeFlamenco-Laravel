<?php

namespace Tests\Unit;

use Testcontainers\Container\GenericContainer;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\TestCase;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
class EmpresaControllerApiTest extends TestCase
{
    use RefreshDatabase;

    protected $postgresContainer;

    protected function setUp(): void
    {
        parent::setUp();

        // Iniciar contenedor de PostgreSQL
        $this->postgresContainer = (new GenericContainer('postgres:alpine'))
            ->withExposedPorts(5432)
            ->withEnv('POSTGRES_USER', 'postgres')
            ->withEnv('POSTGRES_PASSWORD', 'password')
            ->withEnv('POSTGRES_DB', 'test_database')
            ->start();

        // Configurar la conexión de Laravel al contenedor PostgreSQL
        $dbHost = $this->postgresContainer->getHost();
        $dbPort = $this->postgresContainer->getMappedPort(5432);

        // Cambiar las variables en el archivo .env para conectar con el contenedor
        putenv("DB_HOST={$dbHost}");
        putenv("DB_PORT={$dbPort}");
        putenv("DB_DATABASE=test_database");

        // Esperar hasta que el contenedor de Postgres esté disponible
        $this->waitForPostgres();
    }

    protected function waitForPostgres()
    {
        $maxRetries = 10;
        $tries = 0;

        while ($tries < $maxRetries) {
            try {
                // Intentar conectar a la base de datos
                $pdo = new \PDO("pgsql:host={$this->postgresContainer->getHost()};port={$this->postgresContainer->getMappedPort(5432)};dbname=test_database", 'postgres', 'password');
                break;
            } catch (\PDOException $e) {
                $tries++;
                sleep(1); // Esperar antes de reintentar
            }
        }
    }

    protected function tearDown(): void
    {
        // Detener contenedor de PostgreSQL después de las pruebas
        $this->postgresContainer->stop();

        parent::tearDown();
    }

    #[Test]
    public function test_get_all_empresas_with_seeded_data()
    {
        // Ejecutar los seeders para llenar la base de datos con datos de prueba
        Artisan::call('db:seed');

        // Realizar una solicitud GET a la API para obtener todas las empresas
        $response = $this->getJson('/api/empresas');

        // Verificar que la respuesta tenga un código de estado 200
        $response->assertStatus(200);

        // Verificar que la respuesta contenga datos (debe haber al menos una empresa)
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',  // Cambiar según los campos reales de la empresa
                    'cif',   // Cambiar según los campos reales de la empresa
                    // Añadir más campos de la empresa según corresponda
                ]
            ]
        ]);

        // Verificar que haya al menos una empresa en la base de datos
        $this->assertDatabaseHas('empresas', [
            'name' => 'Empresa de Prueba',  // Asegúrate de que este nombre coincida con los datos de prueba en el seeder
        ]);
    }
    public function testGetByNombre()
    {

    }
    public function testCreate()
    {

    }

    public function testDestroy()
    {

    }

    public function testGetById()
    {

    }

    public function testUpdate()
    {

    }

    public function testGetByCif()
    {

    }
}
