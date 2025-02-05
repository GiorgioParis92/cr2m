<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class DebugModeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::id() === 1) {
            Config::set('app.debug', true);
        } else {
            Config::set('app.debug', false);
        }

        return $next($request);
    }
}
