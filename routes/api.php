<?php

use App\Http\Controllers\Api\ClienteApiController;
use App\Http\Controllers\Api\EventosApiController;
use App\Http\Controllers\Api\TicketApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\EmpresaControllerApi;
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

Route::apiResource('empresas',EmpresaControllerApi::Class);
Route::get('empresas',[EmpresaControllerApi::class, 'getAll']);
Route::get('empresas/{id}', [EmpresaControllerApi::class, 'getById']);
Route::get('empresas/nombre/{nombre}', [EmpresaControllerApi::class, 'getByNombre']);
Route::get('empresas/cif/{cif}', [EmpresaControllerApi::class,'getByCif']);
Route::post('empresas',[EmpresaControllerApi::class, 'create']);
Route::put('empresas/{id}', [EmpresaControllerApi::class, 'update']);
Route::delete('empresas/{id}', [EmpresaControllerApi::class, 'delete']);
