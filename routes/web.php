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
    Route::get('/{id}',[VentaController::class, 'show'])->name('ventas.show')->middleware('can:admin');
    Route::get('/', [VentaController::class,'index'])->name('ventas.index')->middleware('can:admin');
});

Route::prefix('empresa')->group(function () {
    Route::get('/create', [EmpresaController::class, 'create'])->name('empresas.create');
    Route::post('/store', [EmpresaController::class, 'store'])->name('empresas.store');
    Route::get('/createadmin', [EmpresaController::class, 'create'])->name('empresas.create-admin')->middleware("can:admin");
    Route::get('/', [EmpresaController::class, 'index'])->name('empresas.index');
    Route::get('/actualizar/{id}', [EmpresaController::class, 'edit'])->name('empresas.edit')->middleware("can:admin");
    Route::post('/empresas/validate', [EmpresaController::class, 'validateField'])->name('empresas.validate')->middleware("can:admin");

    Route::put('/{id}', [EmpresaController::class, 'update'])->name('empresas.update');
    Route::delete('/{id}', [EmpresaController::class, 'destroy'])->name('empresas.destroy');
    // Ruta para buscar por ID
    Route::get('/{id}', [EmpresaController::class, 'show'])
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
    Route::get('/create', [EventosController::class, 'create'])->name('eventos.create')->middleware('can:empresa');
    Route::post('/', [EventosController::class, 'store'])->name('eventos.store')->middleware('can:empresa');
    Route::get('/', [EventosController::class, 'getAll'])->name('eventos.index');
    Route::get('/index-admin', [EventosController::class, 'index'])->name('eventos.index-admin')->middleware('can:admin');
    Route::get('/{id}', [EventosController::class, 'show'])->name('eventos.show');
    Route::get('/{id}/edit', [EventosController::class, 'edit'])->name('eventos.edit')->middleware('can:admin');
    Route::get('/' , [EventosController::class, 'update'])->name('eventos.update')->middleware('can:admin');
    Route::put('/{id}', [EventosController::class, 'update'])->name('eventos.update')->middleware('can:admin');
    Route::delete('/{id}', [EventosController::class, 'destroy'])->name('eventos.destroy')->middleware('can:admin');
});

Route::prefix('clientes')->group(function () {
    Route::post('/', [ClienteController::class, 'store'])->name('clientes.store');
    Route::get('/create', [ClienteController::class, 'create'])->name('clientes.create');
    Route::get('/', [ClienteController::class, 'index'])->name('clientes.index')->middleware('can:admin');
    Route::get('/{id}', [ClienteController::class, 'show'])->name('clientes.show')->middleware('can:admin');
    Route::get('/{id}/edit', [ClienteController::class, 'edit'])->name('clientes.edit')->middleware('can:admin');
    Route::put('/{id}', [ClienteController::class, 'update'])->name('clientes.update')->middleware('can:admin');
    Route::delete('/{id}', [ClienteController::class, 'destroy'])->name('clientes.destroy')->middleware('can:admin');


});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{idEvent}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{idEvent}', [CartController::class, 'remove'])->name('cart.remove');

Route::middleware('auth')->group(function () {
    Route::post('/checkout/stripe', [StripeController::class, 'checkout'])->name('stripe.checkout');
    Route::get('/checkout/success', [StripeController::class, 'success'])->name('stripe.success');
});

Route::middleware('auth')->group(function () {
    Route::get('/tickets/mistickets', [TicketController::class , 'index'])->name('tickets.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/miseventos', [EventosController::class , 'showMeEvents'])->name('eventos.index-me');
});

require __DIR__.'/auth.php';


