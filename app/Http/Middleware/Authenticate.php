<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request)
    {
        if (! $request->expectsJson()) {
            if ($request->path() === 'oauth/authorize') {
                if (isset($request->query()['client_id'])) {
                    $params = [
                        'client_id' => $request->query('client_id'),
                        'return_to' => $request->getRequestUri(),
                    ];

                    Session::put('redirect_data', $params);

                    // Redirect with a single header
                    return redirect()->to('api/login');
                } else {
                    // Redirect with a single header
                    return redirect()->to('/');
                }
            }
            // Redirect with a single header
            return redirect()->to('/');
        }
    }
}
