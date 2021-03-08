<?php

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;
use Stevebauman\Location\Facades\Location;

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
    return view('welcome');
});

Route::post('/register', [AdminController::class, 'index']);

Route::get('get-my-location', function() {
    $location = Location::get();
    return view('location', compact('location'));
});

Route::get('remove-spec', function () {
    function clean($string) {
        $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
     
        return preg_replace('/[^0-9\.]+/', '', $string); // Removes special chars.
    }

     return isValidAmount('30,00.00');
});