<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request)
    {
        \Log::info('Redirecting with params: ' . json_encode($request->path())); // Log the params
        if (! $request->expectsJson()) {
            // Start modified line
            if ($request->path() === 'oauth/authorize') {
                if (isset($request->query()['client_id'])) {
                    $params = array(
                        'client_id' => $request->query()['client_id'],
                        'return_to' => (new \Illuminate\Http\Request)->getRequestUri(),
                    );
                    \Log::info('Redirecting with params: ' . json_encode($params)); // Log the params
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
