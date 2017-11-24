<?php

namespace Bantenprov\LaravelSsoClient;

use Illuminate\Support\ServiceProvider;

class LaravelSsoClientServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
         include __DIR__.'/LaravelSsoClient.php';
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
