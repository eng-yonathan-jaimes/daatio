<?php

namespace Modules\Products\app\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ProductsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::middleware('web')
            ->group(__DIR__.'/../../routes/web.php');
    }
}
