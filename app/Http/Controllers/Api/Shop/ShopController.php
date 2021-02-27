<?php

namespace App\Http\Controllers\Api\Shop;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class ShopController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.jwt');
    }

    public function createShop() {
        $user = auth('api')->user();
        if (! Gate::allows('isMechanic', $user->encodedKey)) {
            abort(403);
        }

        return $user;
    }
}
