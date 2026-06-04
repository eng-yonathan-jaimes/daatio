<?php

use Illuminate\Support\Facades\Route;
use Modules\Clients\app\Http\Controllers\Api\ClientController;
use Modules\Clients\app\Http\Controllers\Api\ClientListOrderController;
use Modules\Clients\app\Http\Controllers\Api\ClientOrderController;
use Modules\Clients\app\Http\Controllers\Api\ClientStateController;

Route::apiResource('clients', ClientController::class);
Route::apiResource('client-states', ClientStateController::class);
Route::apiResource('client-orders', ClientOrderController::class);
Route::apiResource('client-list-orders', ClientListOrderController::class);
