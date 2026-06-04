<?php

namespace Modules\Transactions\app\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class TransactionsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'transactions');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        Route::middleware('web')
            ->prefix('transactions')
            ->name('transactions.')
            ->group(__DIR__.'/../../routes/web.php');

        Route::middleware('api')
            ->prefix('api')
            ->name('api.')
            ->group(__DIR__.'/../../routes/api.php');
    }
}
