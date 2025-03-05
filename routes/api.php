<?php

use App\Http\Controllers\Api\ClienteApiController;
use App\Http\Controllers\Api\EmpresaControllerApi;
use App\Http\Controllers\Api\EventosApiController;

use App\Http\Controllers\Api\LoginControllerApi;
use App\Http\Controllers\Api\TicketApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\VentasApiController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::apiResource('clientes', ClienteApiController::class);
Route::apiResource('users', UserApiController::class);

Route::get('empresas',[EmpresaControllerApi::class, 'getAll']);
Route::post('/login', [LoginControllerApi::class, '__invoke']);
Route::get('tickets', [TicketApiController::class, 'index']);
Route::group([
    'middleware' => ['auth:api', 'admin']
], function () {

    Route::get('tickets/{id}', [TicketApiController::class, 'show']);
    Route::post('tickets', [TicketApiController::class,'store']);
    Route::delete('tickets/{id}', [TicketApiController::class, 'destroy']);

    Route::apiResource('clientes', ClienteApiController::class);
    Route::get('clientes/dni/{dni}', [ClienteApiController::class, 'searchByDni']);
    Route::post('/clientes/email', [ClienteApiController::class, 'searchByEmail']);
    Route::post('clientes/upload-dni/{clienteId}', [ClienteApiController::class, 'uploadDni']);
    Route::apiResource('usuarios',UserApiController::class);
    Route::apiResource('ventas', VentasApiController::class);

    Route::get('eventos/nombre/{nombre}', [EventosApiController::class, 'getByNombre']);

    Route::apiResource('/eventos', EventosApiController::class);
    Route::get('eventos/nombre/{nombre}', [EventosApiController::class, 'getByNombre']);


    Route::get('empresas/{id}', [EmpresaControllerApi::class, 'getById']);
    Route::get('empresas/nombre/{nombre}', [EmpresaControllerApi::class, 'getByNombre']);
    Route::get('empresas/cif/{cif}', [EmpresaControllerApi::class,'getByCif']);
    Route::post('empresas',[EmpresaControllerApi::class, 'create']);
    Route::put('empresas/{id}', [EmpresaControllerApi::class, 'update']);
    Route::delete('empresas/{id}', [EmpresaControllerApi::class, 'destroy']);

    Route::get('ventas', [VentasApiController::class, 'index']);
    Route::get('ventas/{id}', [VentasApiController::class,'show']);
    Route::post('ventas', [VentasApiController::class,'store']);
    Route::delete('ventas/{id}', [VentasApiController::class, 'destroy']);



});



