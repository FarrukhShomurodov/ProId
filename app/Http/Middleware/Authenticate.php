<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            // Start modified line
            if ($request->path() === 'oauth/authorize') {
                if (isset($request->query()['client_id'])) {
                    $params = array(
                        'client_id' => $request->query()['client_id'],
                        'return_to' => (new \Illuminate\Http\Request)->getRequestUri(),
                    );
                    return route('login', $params);
                } else {
                    return route('login');
                }
            }
            // End modified line
            return route('login');
        }
    }
}
