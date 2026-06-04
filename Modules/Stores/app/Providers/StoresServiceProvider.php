<?php

namespace Modules\Stores\app\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class StoresServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'stores');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        Route::middleware('web')
            ->prefix('stores')
            ->name('stores.')
            ->group(__DIR__.'/../../routes/web.php');

        Route::middleware('api')
            ->prefix('api')
            ->name('api.')
            ->group(__DIR__.'/../../routes/api.php');
    }
}
