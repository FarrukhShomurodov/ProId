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

        // Start modified line
        if ($request->path() === 'oauth/authorize') if (isset($request->query()['client_id'])) {
            Session::put('redirect_data', $request->getRequestUri());
        }
        return 'login';
    }
}
