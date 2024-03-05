<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Session;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request): string
    {
        Session::put('test', 'rests');

        // Check if the request is for oauth/authorize and contains client_id
        if ($request->path() === 'oauth/authorize') {
            // Store the request URI in session data
            Session::put('redirect_data', $request->getRequestUri());
        }

        // Always redirect to the login route
        return 'login';
    }
}
