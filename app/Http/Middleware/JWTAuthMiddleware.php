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
        if(!$check_seller) {
            return $next($request);
        }
        
        if($request->routeIs('users.resend') || $request->routeIs('users.verify')) {
            return $next($request);
        }

        if($check_seller->verified_at == null || $check_seller->isVerified != 1) {
            return response()->errorResponse("Business account has not been verified", ["account" => "Please verify user phone number to continue"], 403);
        }

        return $next($request);
    }
}
