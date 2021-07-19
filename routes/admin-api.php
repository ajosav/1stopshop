<?php

use App\Http\Controllers\Api\AbuseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\Mail\MailController;
use App\Http\Controllers\Api\Admin\Auth\AdminAuthController;
use App\Http\Controllers\Api\Admin\Dashboard\AdminDashBoardController;

Route::group(['prefix' => 'admin', 'name' => 'admin'], function () {
    Route::post('login', [AdminAuthController::class, 'login'])->name('login');
    Route::get('refresh-token', [AuthController::class, 'refreshToken'])->name('refresh');
    Route::get('current-user', [AuthController::class, 'authenticatedUser'])->name('current');
    Route::get('logout', [AuthController::class, 'logout'])->name('admin');

    
    Route::middleware('auth.jwt:admin')->group(function () {
        Route::post('send-email', MailController::class);
        Route::get('registered', [AdminDashBoardController::class, 'analytics'])->name('registered');
        Route::get('daily-registered-users', [AdminDashBoardController::class, 'registeredUsers'])->name('daily-registered-users');
        Route::get('count-registered-users', [AdminDashBoardController::class, 'getUsersByRoleCount'])->name('count-registered-users');
        Route::get('sales-analytics', [AdminDashBoardController::class, 'salesAnalytics'])->name('sales-analytics');
        Route::get('all-users', [AdminDashBoardController::class, 'getAllUsers'])->name('registered-users');
    });

    Route::resource('abuse', AbuseController::class);
});