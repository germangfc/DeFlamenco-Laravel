<?php

use App\Http\Controllers\Api\ClienteApiController;
use App\Http\Controllers\Api\EmpresaApiController;
use App\Http\Controllers\Api\EventosApiController;
use App\Http\Controllers\Api\ImagenController;
use App\Http\Controllers\Api\TicketApiController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\VentasController;
use App\Http\Controllers\EmpresaControllerApi;
use App\Http\Controllers\MailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('/ticket', TicketApiController::class);
Route::apiResource('clientes', ClienteApiController::class);
Route::get('clientes/dni/{dni}', [ClienteApiController::class, 'searchByDni']);
Route::post('/clientes/email', [ClienteApiController::class, 'searchByEmail']);
Route::post('clientes/upload-dni/{clienteId}', [ClienteApiController::class, 'uploadDni']);
Route::apiResource('usuarios',\App\Http\Controllers\Api\UserApiController::class);
Route::apiResource('ventas', VentasController::class);

Route::get('eventos/nombre/{nombre}', [EventosApiController::class, 'getByNombre']);

Route::apiResource('empresas',EmpresaApiController::Class);
Route::get('empresas',[EmpresaApiController::class, 'getAll']);
Route::get('empresas/{id}', [EmpresaApiController::class, 'getById']);
Route::get('empresas/nombre/{nombre}', [EmpresaApiController::class, 'getByNombre']);
Route::get('empresas/cif/{cif}', [EmpresaApiController::class,'getByCif']);
Route::post('empresas',[EmpresaApiController::class, 'create']);
Route::put('empresas/{id}', [EmpresaApiController::class, 'update']);
Route::delete('empresas/{id}', [EmpresaApiController::class, 'delete']);


Route::post('/upload-image', [ImagenController::class, 'uploadImage']);
Route::delete('/delete-image/{filePath}', [ImagenController::class, 'deleteImage']);
Route::get('/show-image/{filePath}', [ImagenController::class, 'showImage']);

Route::post('/admin/send-email', [MailController::class, 'sendEmail']);
