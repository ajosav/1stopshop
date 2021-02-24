<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\UserService;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use App\Repositories\OTP\OTPInterface;
use Illuminate\Database\QueryException;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Password;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\OtpValidationRequest;
use App\Http\Requests\Auth\CreateUserRequest;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth.jwt'])->only('resendCode', 'verifyAccount');
    }

    public function authenticate(LoginRequest $request)
    {
        if(auth('api')->check()) {
            auth('api')->logout();
        };
        return $request->login();
    }

    public function createUser(CreateUserRequest $request, UserService $user)
    {
        return $user->createUser($request->validated());
    }

    public function authenticatedUser() {
        return getAuthenticatedUser();
    }

    public function getAuthLink(UserService $userService, $provider) {
        return $userService->generateSocialLink($provider);
    }

    public function handleSocialCallback(UserService $userService, $provider) {
        return $userService->loginViaSocial($provider);
    }

    public function resendCode(OTPInterface $activation_code) {
        $user = auth('api')->user();
        if(!$user->isSeller()) {
            return response()->errorResponse("User account cannot be activated");
        }
        if($user->isSeller()->verified_at == null || $user->isSeller()->isVerified != 1) {
            $activation_code->send();
            return response()->success("Activation code sent to user's mobile");
        }
    }

    public function verifyAccount(OtpValidationRequest $request) {
       return $request->activateUserAccount();        
    }

    public function forgotPassword(Request $request) {
        $request->validate(['email' => 'required|email']);
        
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? response()->success(__($status))
                    : response()->errorResponse(__($status));
    }
}
