<?php

namespace Modules\Users\app\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class UsersServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'users');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        Route::middleware('web')
            ->prefix('users')
            ->name('users.')
            ->group(__DIR__.'/../../routes/web.php');

        Route::middleware('api')
            ->prefix('api/auth')
            ->name('api.auth.')
            ->group(__DIR__.'/../../routes/api.php');
    }
}
