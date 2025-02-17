<?php

use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\EventosApiController;
use App\Http\Controllers\Api\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('/ticket', TicketController::class);
Route::apiResource('clientes', ClienteController::class);
Route::apiResource('usuarios',\App\Http\Controllers\Api\UserController::class);

Route::apiResource('eventos', EventosApiController::class);
Route::get('eventos/nombre/{nombre}', [EventosApiController::class, 'getByNombre']);
