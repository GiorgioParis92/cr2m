<?php

// app/Http/Middleware/SetCssVariables.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\CssVariable;

class SetCssVariables
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $client_id = Auth::user()->client_id;
            $variables='';
            $cssVariables = CssVariable::where('client_id', $client_id)->first();
            if(isset($cssVariables)) {
                $variables = $cssVariables->variables;

            }
            // dd($variables);
            // Share the variables with all views
            view()->share('cssVariables', $variables);
        }

        return $next($request);
    }
}
