<?php

namespace App\Providers;

use App\Http\Controllers\Api\UserApiController;
use App\Http\Middleware\AdminMiddleware;
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

        app('router')->aliasMiddleware('admin', AdminMiddleware::class);
    }
}
