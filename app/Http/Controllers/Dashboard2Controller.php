<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Campagne;

class Dashboard2Controller extends Controller
{
    /**
     * Show the Dashboard2 view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->client_id == 0) {
            $campagnes = Campagne::with('status')->with('docs')->get();
        } else {
            $campagnes = Campagne::with('status')->with('docs')->where('client_id', $user->client_id)->get();
        }
        return view('dashboard2', compact('user', 'campagnes')); // Ensure you have a view named 'dashboard2.blade.php'

    }
}
