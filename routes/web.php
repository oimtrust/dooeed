<?php

use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Middleware\EnsureActiveUser;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');
Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');
Route::get('/reset-password/{token}', fn (string $token) => view('auth.reset-password', ['token' => $token]))->name('password.reset');
Route::view('/dashboard', 'dashboard')->name('dashboard');

Route::post('/admin/session', [SessionController::class, 'store'])
    ->middleware(['auth:sanctum', EnsureActiveUser::class, EnsureAdmin::class, 'throttle:10,1'])
    ->name('admin.session');
Route::delete('/admin/session', [SessionController::class, 'destroy'])->name('admin.session.destroy');

Route::prefix('admin')->name('admin.')->middleware(['auth:web', 'auth.session', EnsureActiveUser::class, EnsureAdmin::class])->group(function (): void {
    Route::view('/users', 'admin.users.index')->name('users.index');
    Route::get('/users/{user}', fn (string $user) => view('admin.users.show', ['userId' => $user]))->whereUuid('user')->name('users.show');
    Route::view('/audit-logs', 'admin.audit')->name('audit.index');
});

Route::post('/session/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('session.login');

Route::post('/session/register', [AuthController::class, 'register'])->middleware('throttle:10,1')->name('session.register');
