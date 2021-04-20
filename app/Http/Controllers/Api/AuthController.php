<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CreateNewUserRequest;
use App\Repositories\OTP\OTPInterface;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\OtpValidationRequest;

class AuthController extends Controller
{
    public $activation_code, $userService, $profileService;
    public function __construct(OTPInterface $activation_code, UserService $userService)
    {
        $this->middleware('auth.jwt')->only('resendCode', 'verifyAccount', 'logout', 'authenticatedUser');
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

    public function createUser(CreateNewUserRequest $request)
    {
        return $this->userService->createUserAccount($request->validated(), $this->activation_code);
    }

    public function authenticatedUser() {
        return getAuthenticatedUser();
    }

    public function getAuthLink($provider, $user_type) {
        return $this->userService->generateSocialLink($provider, $user_type);
    }

    public function handleSocialCallback(UserService $userService, $provider) {
        return $userService->loginViaSocial($provider);
    }

    public function resendCode() {
        $user = auth('api')->user();
        
        if(!$user->email_verified_at) {
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
            'password' => ['required',
                            'string',
                            'confirmed',
                            'min:8', // must be a minimum of 8
                            'regex:/[a-z]/',
                            'regex:/[A-Z]/',
                            'regex:/[0-9]/',
                            'regex:/[@$!%*#?&]/',
            ]
        ]);
            
        return $this->userService->resetPassword($request);
        
    }

    public function logout() {
        if(auth('api')->check()) {
            auth('api')->logout();
            return response()->success('Session ended! Log out was successful');
        };
       return response()->errorResponse('You are not logged in', [], 401);
    }
}
