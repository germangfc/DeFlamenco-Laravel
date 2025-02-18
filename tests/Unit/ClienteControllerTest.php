<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\ClienteApiController;
use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use Mockery;
use PHPUnit\Framework\TestCase;

class ClienteControllerTest extends TestCase
{
    public function test_destroy_cliente_exitoso()
    {
        // Creamos un mock del modelo Cliente
        $clienteMock = Mockery::mock(Cliente::class);
        $clienteMock->shouldReceive('find')->with(1)->andReturnSelf();
        $clienteMock->shouldReceive('delete')->once()->andReturn(true);

        // Creamos una instancia del controlador
        $controller = new ClienteApiController();

        // Llamamos al método destroy y verificamos la respuesta
        $response = $controller->destroy(1);

        // Verificamos que la respuesta sea un JsonResponse con código 200
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->status());
        $this->assertEquals(['message' => 'Cliente eliminado'], $response->getData(true));
    }

    public function test_destroy_cliente_no_existente()
    {
        // Simulamos que no se encuentra el cliente
        $clienteMock = Mockery::mock(Cliente::class);
        $clienteMock->shouldReceive('find')->with(999)->andReturn(null);

        $controller = new ClienteApiController();
        $response = $controller->destroy(999);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->status());
        $this->assertEquals(['message' => 'Cliente no encontrado'], $response->getData(true));
    }
}
