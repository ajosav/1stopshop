<?php

namespace App\Http\Controllers\Api\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Login\LoginRequest;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    public function login(LoginRequest $request) {
        if(auth('api')->check()) {
            auth('api')->logout();
        };
        return $request->login();
    }
}
