<?php

namespace App\Providers;

use App\Models\Mechanic;
use App\Models\ReviewExt;
use Spatie\Macroable\Macroable;
use App\Observers\MechanicObserver;
use App\Repositories\OTP\OTPInterface;
use Illuminate\Support\Facades\Schema;
use App\Repositories\OTP\SendOTPViaSMS;
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

        // $rating_class = (new class() {
        //     use Rating;
        // });

        // $rating_class::macro('reviewImage', function() {
        //     return $this->morphMany(ReviewExt::class, 'imageable');
        // });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        $permissions = config('permission.default_permissions');
        foreach($permissions as $permission) {
            if(!isPermissionExist($permission)) {
                Permission::create(['name' => $permission]);
            }
        }

        Mechanic::observe(MechanicObserver::class);
    }
}
