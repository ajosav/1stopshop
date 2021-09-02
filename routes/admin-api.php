<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\NoteController;
use App\Http\Controllers\Api\AbuseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\Mail\MailController;
use App\Http\Controllers\Api\Admin\Auth\AdminAuthController;
use App\Http\Controllers\Api\Admin\Dashboard\AdminDashBoardController;
use App\Http\Controllers\Api\Admin\EventController;
use App\Http\Controllers\Api\RegisteredUserController;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::post('login', [AdminAuthController::class, 'login'])->name('login');
    Route::get('refresh-token', [AuthController::class, 'refreshToken'])->name('refresh');
    Route::get('current-user', [AuthController::class, 'authenticatedUser'])->name('current');
    Route::get('logout', [AuthController::class, 'logout'])->name('admin');

    
    Route::middleware('auth.jwt:admin')->group(function () {
        Route::post('send-email', MailController::class);
        Route::get('registered', [AdminDashBoardController::class, 'analytics'])->name('registered');
        Route::get('daily-registered-users', [AdminDashBoardController::class, 'registeredUsers'])->name('daily-registered-users');
        Route::get('fetch-users-by-date', [AdminDashBoardController::class, 'fetchUsersWithDate'])->name('fetch-users-with-date');
        Route::get('count-registered-users', [AdminDashBoardController::class, 'getUsersByRoleCount'])->name('count-registered-users');
        Route::get('sales-analytics', [AdminDashBoardController::class, 'salesAnalytics'])->name('sales-analytics');
        Route::get('all-users', [AdminDashBoardController::class, 'getAllUsers'])->name('registered-users');
        Route::get('users', [AdminDashBoardController::class, 'admins'])->name('registered-admins');
        Route::get('all-users/{date}', [AdminDashBoardController::class, 'getUsersByDate'])
                ->name('create-on-date-users')
                ->where(['date' => '[0-9]{4}-[0-9]{2}-[0-9]{2}']);
        Route::get('all-permissions', [AdminDashBoardController::class, 'getPermissions']);
        Route::post('give-permission/{user}', [AdminAuthController::class, 'grantPermission']);
        Route::post('revoke-permission/{user}', [AdminAuthController::class, 'revokePermission']);
        Route::resource('note', NoteController::class);
        Route::post('add-user', [AdminAuthController::class, 'createUser'])->name('register');
        Route::apiResource('income', AccountController::class);
        
        Route::get('get-product-abuses/{adservice}', [AdminDashBoardController::class, 'productAbuses']);
        Route::get('get-product-abuses', [AdminDashBoardController::class, 'allProductAbuses']);
        Route::get('products-reviews', [AdminDashBoardController::class, 'allProductReviews'])->name('products-reviews');
        Route::get('services-reviews', [AdminDashBoardController::class, 'allMechanicReviews'])->name('services-reviews');

        Route::apiResource('event', EventController::class);
        Route::get('event/find-by-date/{date}', [EventController::class, 'findByDate']);

        Route::delete('delete-product/{adService}', [AdminDashBoardController::class, 'deleteProduct']);
        Route::delete('delete-user/{user}', [AdminDashBoardController::class, 'deleteUser']);

        Route::name('users.')->prefix('users')->group(function () {
            Route::get('soft-deleted', [RegisteredUserController::class, 'getDeletedUsers'])->name('trash');
            Route::get('soft-deleted/{encodedKey}', [RegisteredUserController::class, 'findDeletedUser'])->name('thrash-user');
            Route::get('restore/{encodedKey}', [RegisteredUserController::class, 'restoreUser'])->name('restore');
            Route::delete('permanent-delete/{encodedKey}', [RegisteredUserController::class, 'deletePermanently'])->name('permanent-delete');
            Route::delete('soft-delete/{user}', [RegisteredUserController::class, 'deleteUser'])->name('thrash-completely');
        });

        Route::prefix('appointment')->group(function () {
            Route::get('completed', [AdminDashBoardController::class, 'completedAppointment']);
            Route::get('cancelled', [AdminDashBoardController::class, 'cancelledAppointment']);
            Route::get('pending', [AdminDashBoardController::class, 'pendingAppointments']);
        });
    });

    Route::resource('abuse', AbuseController::class);
});