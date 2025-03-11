<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class ShareClientData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
                $client = Client::where('id', $user->client_id)->first();
            

                View::share('client', $client);
            
        }

        return $next($request);
    }
}
