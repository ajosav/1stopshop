<?php

namespace App\Providers;

use App\Repositories\Password\CustomPasswordBrokerManager;
use Illuminate\Auth\Passwords\PasswordResetServiceProvider;
// use Illuminate\Support\ServiceProvider;

class CustomPasswordResetServiceProvider extends PasswordResetServiceProvider
{
    

    
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        
            $this->app->singleton('auth.password', function ($app) {
                return new CustomPasswordBrokerManager($app);
            });
    
            $this->app->bind('auth.password.broker', function ($app) {
                return $app->make('auth.password')->broker();
            });
        // }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
