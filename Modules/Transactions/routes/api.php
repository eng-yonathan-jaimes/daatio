<?php

use Illuminate\Support\Facades\Route;
use Modules\Transactions\app\Http\Controllers\Api\TransactionController;

Route::apiResource('transactions', TransactionController::class);
