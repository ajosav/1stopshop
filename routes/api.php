<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Shop\ShopController;
use App\Http\Controllers\Api\Profile\ProfileController;
use App\Http\Controllers\Api\Mechanic\MechanicController;
use App\Http\Controllers\Api\PartDealer\PartDealerController;
use App\Http\Controllers\Api\Profile\RegisteredUserController;
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

    // Users authentication a
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


    //  user profile endpoint goes here
    Route::name('profile.')->prefix('profile')->group(function () {
        Route::post('create', [ProfileController::class, 'createProfile'])->name('index');

    });

    // users data retrival 
    Route::get('verified-vendors', [RegisteredUserController::class, 'getAllVerifiedVendors']);
    Route::get('users', [RegisteredUserController::class, 'getAllUsers']);
    Route::get('find-user/{encodedKey}', [RegisteredUserController::class, 'findUser']);
    Route::get('find-users/{user_type}', [RegisteredUserController::class, 'findUserByType']);

    // Mechanic user type
    Route::name('mechanic.')->prefix('mechanic')->group(function () {
        Route::get('/', [MechanicController::class, 'index']);
        Route::get('user/{encodedKey}', [MechanicController::class, 'show']);
    });

    // Part Dealer user type goes here
    Route::name('part-dealer.')->prefix('part-dealer')->group(function () {
        Route::get('/', [PartDealerController::class, 'index']);
        Route::get('user/{encodedKey}', [PartDealerController::class, 'show']);
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
   
});

Route::get('get-details', function() {
    $user = User::find(3);

    return $user->getFullUserDetail();
});