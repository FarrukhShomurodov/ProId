<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SavePreviusUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the request is for oauth/authorize and contains client_id
        if ($request->path() === 'oauth/authorize') {
            // Store the request URI in session data
            Session::put('redirect_data', $request->getRequestUri());
        }

        return $next($request);
    }
}
