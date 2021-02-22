<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1'], function () {
    Route::name('users.')->prefix('users')->group(function () {
        Route::post('create', [AuthController::class, 'createUser'])->name('create');
        Route::post('login', [AuthController::class, 'authenticate'])->name('login');
        Route::get('current_user', [AuthController::class, 'authenticatedUser'])->name('current');
        Route::get('{provider}/generate_link', [AuthController::class, 'getAuthLink']);
        Route::get('callback/{provider}', [AuthController::class, 'handleSocialCallback']);
        Route::get('resend_code', [AuthController::class, 'resendCode'])->name('resend');
        Route::post('verify_account', [AuthController::class, 'verifyAccount'])->name('verify');
    });

    Route::get('home', function () {
        // User::create(['...']);
        return auth('api')->user()->otp->expires_at->lt(now());
        return auth('api')->user()->gererateOTP();
    });
   
});