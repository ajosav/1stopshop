<?php

// use DateTime;
use App\Models\AdService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\Review\ReviewController;
use App\Http\Controllers\Api\RegisteredUserController;
use App\Http\Controllers\Api\Admin\Mail\MailController;
use App\Http\Controllers\Api\Mechanic\MechanicController;
use App\Http\Controllers\Api\PartDealer\PartDealerController;
use App\Http\Controllers\Api\Appointment\AppointmentController;
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
        Route::post('create-with-social', [AuthController::class, 'createUserWithSocial']);
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
    Route::get('get-location',[HomeController::class, 'searchLocation']);

    // Mechanic user type
    Route::name('mechanic.')->prefix('mechanic')->group(function () {
        Route::get('/', [MechanicController::class, 'index']);
        Route::get('/filter-services', [MechanicController::class, 'filterService'])->name('filter');
        Route::post('create', [MechanicController::class, 'store'])->name('create');
        Route::patch('update', [MechanicController::class, 'update'])->name('update');
        Route::get('my-appointments', [AppointmentController::class, 'myAppointment']);
        Route::patch('update-appointment/{id}', [AppointmentController::class, 'update']);
        Route::patch('/edit-schedule', [MechanicController::class, 'editSchedule']);
        Route::get('/get-work-schedule/{mechanic}', [MechanicController::class, 'getWorkingHours']);
        Route::get('/{encodedKey}', [MechanicController::class, 'show']);
    });

    // Part Dealer user type goes here
    Route::name('part-dealer.')->prefix('part-dealer')->group(function () {
        Route::get('/', [PartDealerController::class, 'index']);
        Route::get('{encodedKey}', [PartDealerController::class, 'show']);
        Route::post('create', [PartDealerController::class, 'store'])->name('create');
        Route::patch('update', [PartDealerController::class, 'update'])->name('update');
    });

    Route::name('review.')->prefix('review')->group(function () {
        Route::post('rate-mechanic/{mechanic}', [ReviewController::class, 'rateMechanic']);
        Route::get('reviews/{mechanic}', [ReviewController::class, 'mechanicReviews']);
        Route::get('user-review/{mechanic}', [ReviewController::class, 'userReview']);
        // Route::get('rate-mehanic/{mechanic}', [ReviewController::class, 'reviewMechanicStore']);
        Route::post('rate-product/{adService}', [ReviewController::class, 'rateProduct']);
        Route::get('get-product-reviews/{adService}', [ReviewController::class, 'productReviews']);
    });

    // Product action happens here
    Route::name('product.')->prefix('products')->group(function () {
        Route::get('/', [ProductAdController::class, 'index']);
        Route::post('create', [ProductAdController::class, 'store']);
        Route::patch('update/{ad}', [ProductAdController::class, 'update']);
        Route::get('find-products-by-user/{encodedKey}', [ProductAdController::class, 'find']);
        Route::get('find/{encodedKey}', [ProductAdController::class, 'show'])->name('find');
        Route::get('current-user-products', [ProductAdController::class, 'userProducts']);
        Route::get('search', [ProductAdController::class, 'searchProduct']);
        Route::delete('delete/{encodedKey}', [ProductAdController::class, 'deleteProduct']);
        Route::patch('deactivate/{adservice}', [ProductAdController::class, 'deactivateProduct']);
        Route::patch('activate/{adservice}', [ProductAdController::class, 'activateProduct']);
        Route::post('view-contact', [ProductAdController::class, 'viewContact']);
    });

    
    // Profile action occurs here
    Route::name('profile.')->prefix('profile')->group(function () {
        Route::patch('update', [ProfileController::class, 'profileUpdate']);
        Route::post('upload-profile-image', [ProfileController::class, 'uploadProfile'])->name('profile.image');
    });

    Route::name('category.')->prefix('category')->group(function() {
        Route::get('/', [CategoryController::class, 'fetchParentCategories']);
        Route::post('create', [CategoryController::class, 'createCategory']);
        Route::get('fetch-sub-categories/{category}', [CategoryController::class, 'fetchSubCategories']);
        Route::get('all-categories', [CategoryController::class, 'fetchCatWithSubs']);
    });

    // Route::post('send-email', MailController::class);

    // Route::get('/', function () {
    //     return DateTime::createFromFormat('Y-m-d', '2021-06-27')->format('l');
    // });
   
    require __DIR__.'/admin-api.php';
});



