<?php

use Illuminate\Support\Facades\Route;
use Modules\Stores\app\Http\Controllers\StoresController;

Route::middleware('auth')->group(function () {
    Route::get('/stores', [StoresController::class, 'index'])->name('stores.index');
    Route::get('/stores/create', [StoresController::class, 'create'])->name('stores.create');
    Route::post('/stores/types', [StoresController::class, 'storeType'])->name('stores.types.store');
    Route::post('/stores', [StoresController::class, 'store'])->name('stores.store');
    Route::get('/stores/{id}/edit', [StoresController::class, 'edit'])->name('stores.edit');
    Route::post('/stores/{id}', [StoresController::class, 'update'])->name('stores.update');
    Route::post('/stores/{id}/toggle', [StoresController::class, 'toggle'])->name('stores.toggle');
});
