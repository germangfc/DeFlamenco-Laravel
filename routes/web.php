<?php

use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\ClienteControllerView;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeController;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Auth\Middleware\Authorize;


Route::get('/', function () {
    return view('main');
})->name('main');


Route::post('/checkout', [StripeController::class, "checkout"])->name('checkout');
Route::get('/success', [StripeController::class, 'success'])->name('success');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('empresa')->group(function () {
    Route::get('/create', [EmpresaController::class, 'create'])->name('empresas.create');
    Route::post('/store', [EmpresaController::class, 'store'])->name('empresas.store');    Route::get('/', [EmpresaController::class, 'index'])->name('empresas.index');
    Route::get('/actualizar/{id}', [EmpresaController::class, 'edit'])->name('empresas.edit');

    // Actualizar empresa (PUT o PATCH)
    Route::put('/{id}', [EmpresaController::class, 'update'])->name('empresas.update');
    Route::delete('/{id}', [EmpresaController::class, 'destroy'])->name('empresas.destroy');
    // Ruta para buscar por ID
    Route::get('/{id}', [EmpresaController::class, 'show'])
        ->where('id', '[0-9]+') // Solo acepta números para ID
        ->name('empresas.show');
    // Ruta para buscar por nombre
    Route::get('/nombre/{nombre}', [EmpresaController::class, 'showByNombre'])
        ->where('nombre', '[a-zA-Z0-9\s\-]+')
        ->name('empresas.showByNombre');
    // Ruta para buscar por CIF
    Route::get('/cif/{cif}', [EmpresaController::class, 'showByCif'])
        ->where('cif', '[A-Za-z0-9]+') // Regla básica para CIF
        ->name('empresas.showByCif');
});

Route::prefix('clientes')->group(function () {
    Route::get('/', [ClienteControllerView::class, 'index'])->name('clientes.index');
    Route::get('/crear', [ClienteControllerView::class, 'create'])->name('clientes.create');
    Route::post('/', [ClienteControllerView::class, 'store'])->name('clientes.store');
    Route::get('/{id}', [ClienteControllerView::class, 'show'])->name('clientes.show');
    Route::get('/{id}/editar', [ClienteControllerView::class, 'edit'])->name('clientes.edit');
    Route::put('/{id}', [ClienteControllerView::class, 'update'])->name('clientes.update');
    Route::delete('/{id}', [ClienteControllerView::class, 'destroy'])->name('clientes.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
