<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class CheckTemporaryPassword
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if ($user && Session::get('is_temporary_password')) {
            return $next($request);
        }

        // Redirect if not using a temporary password
        return redirect()->route('home')->with('error', 'Unauthorized access.');
    }
}
