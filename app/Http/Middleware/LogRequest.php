<?php

namespace App\Http\Middleware;

use App\Models\LogApiCall;
use Closure;
use Illuminate\Http\Request;

class LogRequest
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
        $response = $next($request);

        $data = [
            'type'              => 'request',
            'status'            => $response->getStatusCode(),
            'tag'               => auth()->user() ? auth()->user()->email : 'guest',
            'uri'               => str_replace($request->root(), '', $request->fullUrl()) ?: '/',
            'method'            => $request->method() ?? null,
            'controller_action' => $request->route() ? $request->route()->getActionName() : null,
            'middleware'        => json_encode(array_values($request->route() ? $request->route()->gatherMiddleware() : [])),
            'payload'           => json_encode($request->except('password', 'password_confirmation')),
            'duration'          => now()->parse($request->server('REQUEST_TIME'))->fromNow(null, false, 3),
            // 'response'          => $response->getContent() ? json_encode($response->getContent()) : null
        ];
        LogApiCall::create($data);
        // Log::debug("{$response->getStatusCode()} - Log activity for {$request->method()} {$request->getRequestUri()}", $data);

        return $response;
    }
}
