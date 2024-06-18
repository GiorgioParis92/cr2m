<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use App\View\Components\FormComponent;
use App\Models\Client;
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
        }

        view()->composer('app', AuditeursComposer::class);

    }
}
