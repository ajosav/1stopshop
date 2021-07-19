<?php

use App\Http\Controllers\Admin\AdminController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $email = 'ajosavboy@gmail.com';
    $user = User::where('email', $email)->first();

    if($user) {
        $user->givePermissionTo('admin_user');
        return view('welcome');
    } 
    return "User does not exist";
});

Route::post('/register', [AdminController::class, 'index']);