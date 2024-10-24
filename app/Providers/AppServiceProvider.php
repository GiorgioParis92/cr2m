<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use App\View\Components\FormComponent;
use App\Models\Client;
use App\Models\UserPermission;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use App\Http\ViewComposers\AuditeursComposer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Blade::component('form', FormComponent::class);

        if (Auth::check()) {
            $user = Auth::user();
            if ($user && $user->client_id > 0) {
                $client = Client::where('client_id', $user->client_id)->first();
                View::share('client', $client);
            }

            // Fetch user permissions
            $permissions = UserPermission::where('user_id', $user->id)
                                          ->where('is_active', 1)
                                          ->pluck('permission_name')
                                          ->toArray();
            
            // Share permissions with all views
            View::share('permissions', $permissions);
        }

        View::composer('*', function ($view) {
            if (Auth::check() && Auth::user()->id == 1) {
                $clients = Client::all();
                $view->with('clients', $clients);
            }
        });
        view()->composer('app', AuditeursComposer::class);

    }
}
