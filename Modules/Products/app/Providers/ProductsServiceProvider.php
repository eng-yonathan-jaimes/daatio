<?php

namespace Modules\Products\app\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ProductsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'products');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        Route::middleware('web')
            ->prefix('products')
            ->name('products.')
            ->group(__DIR__.'/../../routes/web.php');

        Route::middleware('api')
            ->prefix('api')
            ->name('api.')
            ->group(__DIR__.'/../../routes/api.php');
    }
}
