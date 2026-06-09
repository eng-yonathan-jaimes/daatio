<?php

use Illuminate\Support\Facades\Route;
use Modules\Products\app\Http\Controllers\Api\ProductController;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('products', ProductController::class);
});
