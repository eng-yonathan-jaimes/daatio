<?php

use Illuminate\Support\Facades\Route;
use Modules\Stores\app\Http\Controllers\StoresController;

Route::get('/', StoresController::class)->name('index');
