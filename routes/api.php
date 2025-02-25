<?php

use App\Http\Controllers\Api\ClienteApiController;
use App\Http\Controllers\Api\EmpresaControllerApi;
use App\Http\Controllers\Api\EventosApiController;
use App\Http\Controllers\Api\ImagenController;
use App\Http\Controllers\Api\TicketApiController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\VentasApiController;
use App\Http\Controllers\Api\VentasController;
use App\Http\Controllers\MailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth',
], function () {


});

Route::apiResource('/ticket', TicketApiController::class);
Route::apiResource('clientes', ClienteApiController::class);
Route::get('clientes/dni/{dni}', [ClienteApiController::class, 'searchByDni']);
Route::post('/clientes/email', [ClienteApiController::class, 'searchByEmail']);
Route::post('clientes/upload-dni/{clienteId}', [ClienteApiController::class, 'uploadDni']);
Route::apiResource('usuarios',UserApiController::class);
Route::apiResource('ventas', VentasApiController::class);

Route::get('eventos/nombre/{nombre}', [EventosApiController::class, 'getByNombre']);

Route::get('empresas',[EmpresaControllerApi::class, 'getAll']);
Route::get('empresas/{id}', [EmpresaControllerApi::class, 'getById']);
Route::get('empresas/nombre/{nombre}', [EmpresaControllerApi::class, 'getByNombre']);
Route::get('empresas/cif/{cif}', [EmpresaControllerApi::class,'getByCif']);
Route::post('empresas',[EmpresaControllerApi::class, 'create']);
Route::put('empresas/{id}', [EmpresaControllerApi::class, 'update']);
Route::delete('empresas/{id}', [EmpresaControllerApi::class, 'destroy']);

Route::apiResource('ventas', VentasApiController::class);

Route::post('/upload-image', [ImagenController::class, 'uploadImage']);
Route::delete('/delete-image/{filePath}', [ImagenController::class, 'deleteImage']);
Route::get('/show-image/{filePath}', [ImagenController::class, 'showImage']);

Route::post('/admin/send-email', [MailController::class, 'sendEmail']);

