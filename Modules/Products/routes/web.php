<?php

use Illuminate\Support\Facades\Route;
use Modules\Products\app\Http\Controllers\ProductsController;

Route::middleware('auth')->group(function () {
    Route::get('/products', [ProductsController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductsController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductsController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [ProductsController::class, 'edit'])->name('products.edit');
    Route::post('/products/{id}', [ProductsController::class, 'update'])->name('products.update');
    Route::post('/products/{id}/delete', [ProductsController::class, 'destroy'])->name('products.destroy');
});
