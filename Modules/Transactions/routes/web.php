<?php

use Illuminate\Support\Facades\Route;
use Modules\Transactions\app\Http\Controllers\TransactionsController;

Route::middleware('auth')->group(function () {
    Route::get('/transactions', [TransactionsController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/new', [TransactionsController::class, 'quick'])->name('transactions.quick');
    Route::post('/transactions/quick', [TransactionsController::class, 'quickStore'])->name('transactions.quick.store');
    Route::get('/customers/{customer}/transactions/create', [TransactionsController::class, 'create'])->name('transactions.create');
    Route::post('/customers/{customer}/transactions', [TransactionsController::class, 'store'])->name('transactions.store');
});
