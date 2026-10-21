<?php

namespace App\Providers;

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Blade::if('role', function (string $role) {
            return auth()->check() && auth()->user()->role === $role;
        });

        $this->app['router']->aliasMiddleware('role', RoleMiddleware::class);
    }
}
