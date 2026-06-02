<?php

use Illuminate\Support\Facades\Route;
use Modules\Products\app\Http\Controllers\ProductsController;

Route::get('/', ProductsController::class)->name('index');
