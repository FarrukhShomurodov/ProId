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
//        if (! $request->expectsJson()) {
//            // Start modified line
//            if ($request->path() === 'oauth/authorize') {
//                if (isset($request->query()['client_id'])) {
//                    $params = [
//                        'client_id' => $request->query('client_id'),
//                        'return_to' => $request->getRequestUri(),
//                    ];
//
//                    $queryString = http_build_query($params);
//
//                    return redirect()->to('api/login?' . $queryString);
//                } else {
//                    return route('login');
//                }
//            }
//            // End modified line
//            return route('login');
//        }
    }
}
