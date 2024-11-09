<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Campagne;
use App\Notifications\WebPushNotification;

class DashboardController extends Controller
{
    /**
     * Show the Dashboard2 view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $user->notify(new WebPushNotification());
    
        return view('dashboard', compact('user')); // Ensure you have a view named 'dashboard2.blade.php'

    }
}
