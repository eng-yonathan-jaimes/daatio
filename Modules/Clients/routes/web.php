<?php

use Illuminate\Support\Facades\Route;
use Modules\Clients\app\Http\Controllers\ClientsController;

Route::get('/', ClientsController::class)->name('index');
