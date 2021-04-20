<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Shop\ShopController;
use App\Http\Controllers\Api\RegisteredUserController;
use App\Http\Controllers\Api\Profile\ProfileController;
use App\Http\Controllers\Api\Mechanic\MechanicController;
use App\Http\Controllers\Api\PartDealer\PartDealerController;
use App\Http\Controllers\Api\ProductService\ProductAdController;

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

    // Users authentication
    Route::name('users.')->prefix('users')->group(function () {
        Route::post('create', [AuthController::class, 'createUser'])->name('create');
        Route::post('login', [AuthController::class, 'authenticate'])->name('login');
        Route::get('current-user', [AuthController::class, 'authenticatedUser'])->name('current');
        Route::get('{provider}/{user_type}/generate_link', [AuthController::class, 'getAuthLink']);
        Route::get('callback/{provider}', [AuthController::class, 'handleSocialCallback']);
        Route::post('verify-account', [AuthController::class, 'verifyAccount'])->name('verify');
        Route::get('resend-code', [AuthController::class, 'resendCode'])->name('resend');
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.request');
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
        Route::get('logout', [AuthController::class, 'logout']);
    });

    // users data retrival
    Route::get('users', [RegisteredUserController::class, 'index']);
    Route::get('find-user/{encodedKey}', [RegisteredUserController::class, 'findUser']);
    Route::get('find-users/{user_type}', [RegisteredUserController::class, 'findUserByType']);

    // Mechanic user type
    Route::name('mechanic.')->prefix('mechanic')->group(function () {
        Route::get('/', [MechanicController::class, 'index']);
        Route::get('{encodedKey}', [MechanicController::class, 'show']);
        Route::post('create', [MechanicController::class, 'store'])->name('create');
    });

    // Part Dealer user type goes here
    Route::name('part-dealer.')->prefix('part-dealer')->group(function () {
        Route::get('/', [PartDealerController::class, 'index']);
        Route::get('{encodedKey}', [PartDealerController::class, 'show']);
        Route::post('create', [PartDealerController::class, 'store'])->name('create');
    });

    // Product action happens here
    Route::name('product.')->prefix('products')->group(function () {
        Route::get('/', [ProductAdController::class, 'index']);
        Route::post('create', [ProductAdController::class, 'store']);
        Route::get('find-products-by-user/{encodedKey}', [ProductAdController::class, 'find']);
        Route::get('find/{encodedKey}', [ProductAdController::class, 'show'])->name('find');
        Route::get('current-user-products', [ProductAdController::class, 'userProducts']);
        Route::get('search', [ProductAdController::class, 'searchProduct']);
    });

    Route::get('remove', function() {
        $user = auth('api')->user();
    
        $user->revokePermissionTo('part dealer');
        $user->partDealer()->delete();
    
        return "Success";
    
    })->middleware('auth.jwt');
   
});




Route::get('date', function() {
    date('Y-m-d', strtotime($date));
});