<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\LoginResponse;
use App\Http\Controllers\Auth\LoginResponse as CustomLoginResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LoginResponse::class, CustomLoginResponse::class);
    }

    public function boot(): void
    {
        // Reintenta conexión a MySQL si se perdió
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            DB::reconnect();
        }

        Schema::defaultStringLength(191);
    }
}

