<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\Auth\AuthenticateUser;

class JWTAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $autheticate = new AuthenticateUser();

        if(!$autheticate->authenticate()) {
            return $autheticate->authFailed();
        }

        $user = auth('api')->user();

        if($request->routeIs('users.resend') || $request->routeIs('users.verify')) {
            return $next($request);
        }

        if($user->email_verified_at == null) {
            return response()->errorResponse("Account has not been activativated", ["account" => "Please activate user account to continue"], 403);
        }

        return $next($request);
    }
}
