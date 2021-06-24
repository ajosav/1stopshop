<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Helpers\ResourceHelpers;
use App\Http\Controllers\Controller;
use App\Repositories\OTP\OTPInterface;
use App\Http\Requests\Auth\LoginRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\OtpValidationRequest;
use App\Http\Requests\Auth\CreateNewUserRequest;
use App\Http\Requests\Auth\Social\CreateUserRequest;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;

class AuthController extends Controller
{
    public $activation_code, $userService, $profileService;
    public function __construct(OTPInterface $activation_code, UserService $userService)
    {
        $this->middleware('auth.jwt')->only('resendCode', 'verifyAccount', 'logout', 'authenticatedUser', 'updateUser');
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

    public function createUserWithSocial(CreateUserRequest $request) {
        $data = $request->validated() + ['email_verified_at' => date('Y-m-d H:i:s')];
        return $this->userService->createUserWith($data);
    }

    public function refreshToken() {
        try {
            if(!$token = auth('api')->refresh()) {
                return response()->errorResponse('Unable to refresh token');
            }
            $user = auth('api')->user();
            return ResourceHelpers::returnAuthenticatedUser($user, "User Token successfully refreshed");
        } catch(TokenBlacklistedException $e) {
            return response()->errorResponse('Token has already been refreshed and invalidated', ["token" => $e->getMessage()]);
        } catch (TokenInvalidException $e) {
            return response()->errorResponse('Token has already been refreshed and invalidated', ["token" => $e->getMessage()]);            
        } catch (JWTException $e) {
            return response()->errorResponse('Please pass a bearer token', ["token" => $e->getMessage()]);
    
        }
        
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
