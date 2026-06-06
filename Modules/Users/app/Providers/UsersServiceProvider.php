<?php

namespace Modules\Users\app\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class UsersServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::middleware('api')
            ->prefix('api/auth')
            ->name('api.auth.')
            ->group(__DIR__.'/../../routes/api.php');
    }
}
