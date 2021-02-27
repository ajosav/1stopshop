<?php

namespace App\Http\Controllers\Api\Shop;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class ShopController extends Controller
{
    public $auth_ser;
    
    public function __construct()
    {
        $this->middleware('auth.jwt');
        $this->auth_user = auth('api')->user();
    }

    public function createShop() {
        $user = auth('api')->user();
        if (Gate::allows('isMechanic', $user->encodedKey)) {
            abort(403, "Access Denied! User now allowed to access resource");
        }

        return $user;
    }
}
