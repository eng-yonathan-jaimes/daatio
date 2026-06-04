<?php

use Illuminate\Support\Facades\Route;
use Modules\Users\app\Http\Controllers\Api\AuthController;
use Modules\Users\app\Http\Controllers\Api\UserController;

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/me', [AuthController::class, 'me'])->name('me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::apiResource('users', UserController::class);
});
