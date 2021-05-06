<?php

namespace App\Providers;

use App\Models\Mechanic;
use App\Observers\MechanicObserver;
use App\Repositories\OTP\OTPInterface;
use Illuminate\Support\Facades\Schema;
use App\Repositories\OTP\SendOTPViaSMS;
use Illuminate\Support\ServiceProvider;
use App\Repositories\OTP\SendOTPViaMail;
use App\Services\AdProductService\AdProductActionService;
use Spatie\Permission\Models\Permission;

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
