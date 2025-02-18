<?php

use App\Http\Controllers\Api\ClienteController;
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
    Route::get('/create', [EmpresaController::class, 'create'])->name('empresas.create'); // Debe ir antes
    Route::get('/', [EmpresaController::class, 'index'])->name('empresas.index');
    Route::get('/actualizar/{id}', [EmpresaController::class, 'edit'])->name('empresas.edit');

    // Actualizar empresa (PUT o PATCH)
    Route::put('/{id}', [EmpresaController::class, 'update'])->name('empresas.update');
    Route::get('/eliminar/{id}', [EmpresaController::class, 'destroy'])->name('empresas.eliminar');
    Route::get('/{id}', [EmpresaController::class, 'show'])->name('empresas.show'); // Debe ir al final
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
