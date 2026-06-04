<?php

use Illuminate\Support\Facades\Route;
use Modules\Stores\app\Http\Controllers\Api\StoreController;
use Modules\Stores\app\Http\Controllers\Api\StoreTypeController;

Route::apiResource('store-types', StoreTypeController::class);
Route::apiResource('stores', StoreController::class);
