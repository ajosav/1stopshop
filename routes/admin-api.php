<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\Auth\AdminAuthController;
use App\Http\Controllers\Api\Admin\Dashboard\AdminDashBoardController;

Route::group(['prefix' => 'admin', 'name' => 'admin'], function () {
    Route::post('login', [AdminAuthController::class, 'login'])->name('login');
    Route::get('refresh-token', [AuthController::class, 'refreshToken'])->name('refresh');
    Route::get('current-user', [AuthController::class, 'authenticatedUser'])->name('current');
    Route::get('logout', [AuthController::class, 'logout'])->name('admin');

    
    Route::middleware('auth.jwt:admin')->group(function () {
        Route::get('registered', [AdminDashBoardController::class, 'analytics'])->name('registered');
    });
});