<?php

use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ClienteControllerView;
use App\Http\Controllers\Api\ClienteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeController;

Route::get('/', function () {
    return view('main');
})->name('main');

Route::post('/checkout', [StripeController::class, "checkout"])->name('checkout');
Route::get('/success', [StripeController::class, 'success'])->name('success');

Route::prefix('empresa')->group(function () {
    Route::get('/create', [EmpresaController::class, 'create'])->name('empresas.create'); // Debe ir antes
    Route::get('/', [EmpresaController::class, 'index'])->name('empresas.index');
    Route::get('/actualizar/{id}', [EmpresaController::class, 'edit'])->name('empresas.edit');

    Route::put('/{id}', [EmpresaController::class, 'update'])->name('empresas.update');
    // Actualizar empresa (PUT o PATCH)
    Route::get('/eliminar/{id}', [EmpresaController::class, 'destroy'])->name('empresas.eliminar');
    Route::get('/{id}', [EmpresaController::class, 'show'])->name('empresas.show'); // Debe ir al final
});

Route::prefix('clientes')->group(function () {
    Route::get('/', [ClienteControllerView::class, 'index'])->name('clientes.index');
    Route::get('/crear', [ClienteControllerView::class, 'create'])->name('clientes.create');
    Route::post('/', [ClienteControllerView::class, 'store'])->name('clientes.store');
    Route::get('/{id}', [ClienteControllerView::class, 'show'])->name('clientes.show');
    Route::get('/{id}/editar', [ClienteControllerView::class, 'edit'])->name('clientes.edit');
    Route::put('/{id}', [ClienteControllerView::class, 'update'])->name('clientes.update');
});
    Route::delete('/{id}', [ClienteControllerView::class, 'destroy'])->name('clientes.destroy');
