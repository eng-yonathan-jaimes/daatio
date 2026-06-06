<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\PhoneVerificationController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('/email/verify', [EmailVerificationController::class, 'show'])->name('verification.notice');
Route::post('/email/verify', [EmailVerificationController::class, 'verify'])->name('verification.verify');
Route::post('/email/resend', [EmailVerificationController::class, 'resend'])->name('verification.resend');

Route::get('/phone/verify', [PhoneVerificationController::class, 'show'])->name('phone.verification.notice');
Route::post('/phone/verify', [PhoneVerificationController::class, 'verify'])->name('phone.verification.verify');
Route::post('/phone/resend', [PhoneVerificationController::class, 'resend'])->name('phone.verification.resend');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::post('/subscription/renew', [SubscriptionController::class, 'renew'])->name('subscription.renew');

    Route::get('/print/customers', [PrintController::class, 'customerList'])->name('print.customers');
    Route::get('/print/transactions', [PrintController::class, 'transactions'])->name('print.transactions');
    Route::get('/print/customers/{id}', [PrintController::class, 'customerHistory'])->name('print.customer');
});
