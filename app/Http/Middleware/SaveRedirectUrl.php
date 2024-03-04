<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaveRedirectUrl
{
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            // Пользователь аутентифицирован, сохраняем текущий URL
            session(['redirect_url' => url()->previous()]);
        }

        return $next($request);
    }
}
