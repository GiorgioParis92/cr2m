<?php
// app/Http/Controllers/MenuController.php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $menus = Menu::with(['children' => function ($query) use ($user) {
            $query->where(function ($q) use ($user) {
                $q->whereNull('permission')
                  ->orWhereHas('permissions', function ($query) use ($user) {
                      $query->whereIn('name', $user->getAllPermissions()->pluck('name')->toArray());
                  });
            });
        }])->where(function ($q) use ($user) {
            $q->whereNull('permission')
              ->orWhereHas('permissions', function ($query) use ($user) {
                  $query->whereIn('name', $user->getAllPermissions()->pluck('name')->toArray());
              });
        })->where('parent_id', null)->get();

        return view('frontend.sidebar', compact('menus'));
    }
}
