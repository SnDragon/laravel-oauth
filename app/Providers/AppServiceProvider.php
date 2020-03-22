<?php

namespace App\Providers;

use App\Support\ClientManager;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('oauth.manager', function ($app){
           return new ClientManager($app);
        });

        $this->app->singleton('http.client', function() {
            return new \GuzzleHttp\Client();
        });
    }
}
