<?php

namespace App\Providers;

use App\Http\Controllers\Api\UserApiController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        // Aquí puedes registrar tus middleware
        Route::middleware('admin')->group(function () {
            // Definir rutas que requieren el middleware de admin
            Route::apiResource('usuarios', UserApiController::class);
        });
    }
}
