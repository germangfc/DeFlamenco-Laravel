<?php

use App\Http\Controllers\Api\ClienteApiController;
use App\Http\Controllers\Api\EmpresaApiController;
use App\Http\Controllers\Api\EventosApiController;
use App\Http\Controllers\Api\TicketApiController;
use App\Http\Controllers\Api\UserApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('tickets', TicketApiController::class);
Route::apiResource('clientes', ClienteApiController::class);
Route::apiResource('usuarios',UserApiController::class);
Route::apiResource('eventos', EventosApiController::class);

Route::get('eventos/nombre/{nombre}', [EventosApiController::class, 'getByNombre']);

Route::apiResource('empresas',EmpresaApiController::Class);
Route::get('empresas',[EmpresaApiController::class, 'getAll']);
Route::get('empresas/{id}', [EmpresaApiController::class, 'getById']);
Route::get('empresas/nombre/{nombre}', [EmpresaApiController::class, 'getByNombre']);
Route::get('empresas/cif/{cif}', [EmpresaApiController::class,'getByCif']);
Route::post('empresas',[EmpresaApiController::class, 'create']);
Route::put('empresas/{id}', [EmpresaApiController::class, 'update']);
Route::delete('empresas/{id}', [EmpresaApiController::class, 'delete']);
