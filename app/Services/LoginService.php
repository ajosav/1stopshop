<?php

namespace App\Services;

use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginService {
    public $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate()
    {
        $this->ensureIsNotRateLimited();

        try {
            if (! $token = auth('api')->attempt($this->credentials())) {
                // dd($token);
                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'email' => "Either the email or password provided does not match",
                ]);
            }
        } catch (JWTException $e) {
            return response()->errorResponse("Error Generating Token", ["errorDetails" => $e->getMessage()]);
        }
        

        RateLimiter::clear($this->throttleKey());
        $user = auth('api')->user();
        $token_response = array_merge(respondWithToken($token), $user->getFullUserDetail());
        return response()->success('User Successfully Authenticated', $token_response);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this->request));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::lower($this->request->email).'|'.$this->request->ip();
    }

    public function credentials(array $credentials = []) {
        if(empty($credentials)) {
            return $this->request->only('email', 'password');
        }
        return $credentials;
    }
}