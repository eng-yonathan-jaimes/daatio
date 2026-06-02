<?php

namespace Modules\Clients\app\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ClientsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'clients');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        Route::middleware('web')
            ->prefix('clients')
            ->name('clients.')
            ->group(__DIR__.'/../../routes/web.php');
    }
}
