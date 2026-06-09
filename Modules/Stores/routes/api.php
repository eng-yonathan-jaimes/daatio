<?php

use Illuminate\Support\Facades\Route;
use Modules\Stores\app\Http\Controllers\Api\StoreController;
use Modules\Stores\app\Http\Controllers\Api\StoreTypeController;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('stores', StoreController::class);
    Route::apiResource('store-types', StoreTypeController::class);
});
