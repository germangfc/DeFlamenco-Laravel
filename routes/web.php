<?php

use App\Http\Controllers\Api\ClienteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
/*
Route::get('/clientes', [ClienteController::class, 'getAll']);
Route::get('/clientes/{id}', [ClienteController::class, 'getById']);
Route::post('/clientes',[ClienteController::class,'store']);
Route::put('/clientes/{id}', [ClienteController::class, 'update']);
Route::delete('/clientes/{id}', [ClienteController::class, 'delete']);
*/

