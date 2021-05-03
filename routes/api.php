<?php

use App\Http\Controllers\Api\Appointment\AppointmentController;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\Shop\ShopController;
use Symfony\Component\HttpKernel\Profiler\Profile;
use App\Http\Controllers\Api\RegisteredUserController;
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
        Route::get('refresh-token', [AuthController::class, 'refreshToken'])->name('refresh');
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
    Route::post('book-appointment', [AppointmentController::class, 'book']);

    // Mechanic user type
    Route::name('mechanic.')->prefix('mechanic')->group(function () {
        Route::get('/', [MechanicController::class, 'index']);
        Route::get('/filter-services', [MechanicController::class, 'filterService'])->name('filter');
        Route::post('create', [MechanicController::class, 'store'])->name('create');
        Route::get('book-appointment', [MechanicController::class, 'bookAppointment']);
        Route::get('/{encodedKey}', [MechanicController::class, 'show']);
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
        Route::get('find-by-category/{catgory_id}', [ProductAdController::class, 'findByCategory']);
    });

    // Profile action occurs here
    Route::name('profile.')->prefix('profile')->group(function () {
        Route::patch('update', [ProfileController::class, 'profileUpdate']);
    });

    Route::name('category.')->prefix('category')->group(function() {
        Route::get('/', [CategoryController::class, 'fetchParentCategories']);
        Route::post('create', [CategoryController::class, 'createCategory']);
        Route::get('fetch-sub-categories/{category}', [CategoryController::class, 'fetchSubCategories']);
        Route::get('all-categories', [CategoryController::class, 'fetchCatWithSubs']);
    });

    // Route::get('remove', function() {
    //     $user = auth('api')->user();
    
    //     $user->revokePermissionTo('part dealer');
    //     $user->partDealer()->delete();
    
    //     return "Success";
    
    // })->middleware('auth.jwt');
   
});