<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SSOLoginController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/sso-login', [SSOLoginController::class, 'viaDirect'])->name('sso.login');
    Route::post('/sso-login/{databaseInfo}', [SSOLoginController::class, 'viaID'])->name('sso.login.id');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
