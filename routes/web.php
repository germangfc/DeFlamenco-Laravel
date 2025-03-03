<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EventosController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeController;

Route::get('/', function () {
    return view('main');
})->name('main');



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/ticket/validar/{id}', [TicketController::class, 'validar'])->name('ticket.validar');

Route::prefix('venta')->group(function () {
    Route::get('/{id}',[VentaController::class, 'show'])->name('ventas.show'); //->middleware(['auth','admin']);
    Route::get('/', [VentaController::class,'index'])->name('ventas.index'); //->middleware(['auth','admin']);
});

Route::prefix('empresa')->group(function () {
    Route::get('/create', [EmpresaController::class, 'create'])->name('empresas.create');
    Route::post('/store', [EmpresaController::class, 'store'])->name('empresas.store');
    Route::get('/', [EmpresaController::class, 'index'])->name('empresas.index');
    Route::get('/actualizar/{id}', [EmpresaController::class, 'edit'])->name('empresas.edit');//->middleware("can:admin");
    Route::post('/empresas/validate', [EmpresaController::class, 'validateField'])->name('empresas.validate');

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
        ->where('cif', '[A-Za-z0-9]+')
        ->name('empresas.showByCif');
});

Route::get('/', [EventosController::class, 'getAll'])->name('eventos');
Route::prefix('eventos')->group(function () {
    Route::get('/create', [EventosController::class, 'create'])->name('eventos.create');
    Route::post('/', [EventosController::class, 'store'])->name('eventos.store')->middleware(['auth','admin']);
    Route::get('/', [EventosController::class, 'getAll'])->name('eventos.index');
    Route::get('/index-admin', [EventosController::class, 'index'])->name('eventos.index-admin')->middleware(['auth','admin']);
    Route::get('/{id}', [EventosController::class, 'show'])->name('eventos.show');
    Route::get('/{id}/edit', [EventosController::class, 'edit'])->name('eventos.edit')->middleware(['auth','admin']);
    Route::get('/' , [EventosController::class, 'update'])->name('eventos.update')->middleware(['auth','admin']);
    Route::put('/{id}', [EventosController::class, 'update'])->name('eventos.update')->middleware(['auth','admin']);
    Route::delete('/{id}', [EventosController::class, 'destroy'])->name('eventos.destroy')->middleware(['auth','admin']);
});

Route::prefix('clientes')->group(function () {
    Route::post('/', [ClienteController::class, 'store'])->name('clientes.store');
    Route::get('/create', [ClienteController::class, 'create'])->name('clientes.create');
    Route::get('/', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/{id}', [ClienteController::class, 'show'])->name('clientes.show');
    Route::get('/{id}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
    Route::put('/{id}', [ClienteController::class, 'update'])->name('clientes.update');
    Route::delete('/{id}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
});
    Route::delete('/{id}', [ClienteController::class, 'destroy'])->name('clientes.destroy');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{idEvent}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{idEvent}', [CartController::class, 'remove'])->name('cart.remove');

Route::middleware('auth')->group(function () {
    Route::post('/checkout/stripe', [StripeController::class, 'checkout'])->name('stripe.checkout');
    Route::get('/checkout/success', [StripeController::class, 'success'])->name('stripe.success');
});


require __DIR__.'/auth.php';


