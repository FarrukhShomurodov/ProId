<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SaveRedirectUrl
{
    public function handle(Request $request, Closure $next)
    {
        // Проверяем, если пользователь не аутентифицирован
        if (!auth()->check()) {
            // Сохраняем текущий URL в сессии
            session(['redirect_url' => $request->fullUrl()]);
        }

        return $next($request);
    }
}
