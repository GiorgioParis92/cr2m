<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\FormModel\DefaultForm;

class FormServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('defaultform', function ($app) {
            return new DefaultForm();
        });
    }

    public function boot()
    {
        //
    }
}
