<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SaveRedirectUrl
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->path() === 'oauth/authorize') {
            $request->session()->put('redirect_data', $request->getRequestUri());
        }

        return $next($request);
    }
}
