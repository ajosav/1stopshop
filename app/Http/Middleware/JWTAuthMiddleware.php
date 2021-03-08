<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

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
        $autheticate = getAuthenticatedUser();

        if($autheticate->getStatusCode() != 200) {
            return $autheticate;
        }

        $user = auth('api')->user();
        $check_seller = $user->isSeller();

        if($check_seller) {
        
            if(!$user->userProfile) {
                if($request->routeIs('profile.index')) {
                    return $next($request);
                }
                return response()->errorResponse("User profile has not been setup", ["account" => "Please setup user profile"], 403);
            } else {
                if($request->routeIs('users.resend') || $request->routeIs('users.verify')) {
                    return $next($request);
                }

                if($user->userProfile->verified_at == null || $user->userProfile->isVerified != 1) {
                    return response()->errorResponse("Business account has not been verified", ["account" => "Please verify user email to continue"], 403);
                }
            }
        }

        return $next($request);
    }
}
