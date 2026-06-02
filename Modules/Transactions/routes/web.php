<?php

use Illuminate\Support\Facades\Route;
use Modules\Transactions\app\Http\Controllers\TransactionsController;

Route::get('/', TransactionsController::class)->name('index');
