<?php

use App\Http\Controllers\Api\Admin\AuditLogController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\EmailOtpController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\Wealth\InitialWealthEntryController;
use App\Http\Middleware\EnsureActiveUser;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/auth')->group(function (): void {
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::post('/forgot-password', [PasswordResetController::class, 'forgot'])->middleware('throttle:5,1');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->middleware('throttle:5,1');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('throttle:10,1');

    Route::middleware(['auth:api,web', EnsureActiveUser::class])->group(function (): void {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

Route::post('/email/otp-request', [EmailOtpController::class, 'request'])->middleware('throttle:email-otp');
Route::post('/email/otp-verify', [EmailOtpController::class, 'verify'])->middleware('throttle:10,1');

Route::prefix('v1')->middleware(['auth:api,web', EnsureActiveUser::class])->group(function (): void {
    Route::apiResource('initial-wealth-entries', InitialWealthEntryController::class)->only(['index', 'store', 'update', 'destroy']);
});

Route::prefix('v1/admin')->name('api.admin.')->middleware(['auth:api,web', EnsureActiveUser::class, EnsureAdmin::class])->group(function (): void {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::patch('/users/{user}', [UserController::class, 'update'])->middleware('throttle:30,1')->name('users.update');
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.index');
});
