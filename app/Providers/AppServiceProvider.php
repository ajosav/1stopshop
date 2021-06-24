<?php

namespace App\Providers;

use App\Models\Mechanic;
use App\Models\ReviewExt;
use Spatie\Macroable\Macroable;
use App\Observers\MechanicObserver;
use App\Repositories\OTP\OTPInterface;
use Illuminate\Support\Facades\Schema;
use App\Repositories\OTP\SendOTPViaSMS;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use App\Repositories\OTP\SendOTPViaMail;
use Spatie\Permission\Models\Permission;
use Codebyray\ReviewRateable\Models\Rating;
use App\Services\AdProductService\AdProductActionService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        
        $this->app->singleton(OTPInterface::class, function() {
            return new SendOTPViaMail;
            // return new SendOTPViaSMS;
        });

        $this->app->bind('ad-service', function() {
            return new AdProductActionService;
        });

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Model::preventLazyLoading(! app()->isProduction());

        Schema::defaultStringLength(191);
        Mechanic::observe(MechanicObserver::class);
        
    }
}
