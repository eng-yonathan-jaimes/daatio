<?php

use Illuminate\Support\Facades\Route;
use Modules\Products\app\Http\Controllers\Api\ProductController;

Route::apiResource('products', ProductController::class);
