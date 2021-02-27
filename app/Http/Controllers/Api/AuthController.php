<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Controllers\Controller;
use App\Repositories\OTP\OTPInterface;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\OtpValidationRequest;
use App\Http\Requests\Auth\CreateUserRequest;

class AuthController extends Controller
{
    public $activation_code, $userService;
    public function __construct(OTPInterface $activation_code, UserService $userService)
    {
        $this->middleware('auth.jwt')->only('resendCode', 'verifyAccount');
        $this->activation_code = $activation_code;
        $this->userService = $userService;
    }

    public function authenticate(LoginRequest $request)
    {
        if(auth('api')->check()) {
            auth('api')->logout();
        };
        return $request->login();
    }

    public function createUser(CreateUserRequest $request)
    {
        return $this->userService->createUser($request->validated(), $this->activation_code);
    }

    public function authenticatedUser() {
        return getAuthenticatedUser();
    }

    public function getAuthLink($provider) {
        return $this->userService->generateSocialLink($provider);
    }

    public function handleSocialCallback(UserService $userService, $provider) {
        return $userService->loginViaSocial($provider);
    }

    public function resendCode() {
        $user = auth('api')->user();
        if(!$user->isSeller()) {
            return response()->errorResponse("User account cannot be activated");
        }
        if($user->isSeller()->verified_at == null || $user->isSeller()->isVerified != 1) {
            $this->activation_code->send();
            return response()->success("Activation code sent to user's email");
        }

        return response()->success("User account already activated");
    }

    public function verifyAccount(OtpValidationRequest $request) {
       return $request->activateUserAccount();        
    }

    public function forgotPassword(Request $request) {
        $request->validate(['email' => 'required|email']);
        return $this->userService->sendPasswordResetLink($request);
    }


    public function resetPassword(Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);
            
        return $this->userService->resetPassword($request);
        
    }
}
