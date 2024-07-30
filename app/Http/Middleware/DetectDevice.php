<?php

namespace App\Http\Middleware;

use Closure;
use Detection\MobileDetect;

class DetectDevice
{
    public function handle($request, Closure $next)
    {
        $detect = new MobileDetect;

        if ($detect->isMobile() || $detect->isTablet()) {
            session(['device' => 'mobile']);
        } else {
            session(['device' => 'desktop']);
        }

        return $next($request);
    }
}
