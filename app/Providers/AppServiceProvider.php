<?php

namespace App\Providers;

use App\Http\Controllers\Api\UserApiController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Spatie\Flash\Flash;

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
        app('router')->aliasMiddleware('admin', AdminMiddleware::class);
        Flash::levels([
            'success' => 'bg-green-500 text-white',
            'error' => 'bg-red-500 text-white',
            'warning' => 'bg-yellow-500 text-black',
        ]);
    }
}
