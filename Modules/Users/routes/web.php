<?php

use Illuminate\Support\Facades\Route;
use Modules\Users\app\Http\Controllers\UsersController;

Route::get('/', UsersController::class)->name('index');
