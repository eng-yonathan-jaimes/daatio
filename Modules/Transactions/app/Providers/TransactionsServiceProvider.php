<?php

namespace Modules\Transactions\app\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class TransactionsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::middleware('web')
            ->group(__DIR__.'/../../routes/web.php');
    }
}
