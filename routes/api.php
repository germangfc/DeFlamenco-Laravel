<?php

use App\Http\Controllers\Api\EventosApiController;
use App\Http\Controllers\Api\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\EmpresaControllerApi;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('/ticket', TicketController::class);

Route::apiResource('eventos', EventosApiController::class);
Route::get('eventos/nombre/{nombre}', [EventosApiController::class, 'getByNombre']);

Route::get('empresas',[\App\Http\Controllers\EmpresaControllerApi::class, 'getAll']);


