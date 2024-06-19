<?php 
// app/Http/Middleware/LoadUserRelations.php
namespace App\Http\Middleware;

use Closure;

class LoadUserRelations
{
    public function handle($request, Closure $next)
    {
        if ($user = auth()->user()) {
            $user->load('type','client');
        }

        return $next($request);
    }
}
