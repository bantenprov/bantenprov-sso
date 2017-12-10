<?php

namespace Bantenprov\BantenprovSso;

use Illuminate\Support\ServiceProvider;

class BantenprovSsoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__.'/BantenprovSso.php';
        $this->publishes([
        __DIR__ . '/views'          => base_path('public/js'),
        __DIR__ . '/controllers'    => base_path('app/Http/Controllers'),
        ]);
        $this->commands('Bantenprov\BantenprovSso\Commands\RouteCommands');
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
