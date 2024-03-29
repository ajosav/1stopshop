<?php

namespace App\Providers;

use App\Models\Abuse;
use App\Models\AdService;
use App\Models\FoundHelpful;
use App\Models\Mechanic;
use App\Models\Notification;
use App\Models\ReviewExt;
use App\Observers\AdServiceObserver;
use Spatie\Macroable\Macroable;
use App\Observers\MechanicObserver;
use App\Observers\RatingObserver;
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
        Rating::resolveRelationUsing('abuses', function ($abuseModel) {
            return $abuseModel->morphMany(Abuse::class, 'abusable');
        });
        Rating::resolveRelationUsing('notification', function ($abuseModel) {
            return $abuseModel->morphMany(Notification::class, 'notifiable');
        });
        Rating::resolveRelationUsing('reviewExt', function ($reviewExtModel) {
            return $reviewExtModel->morphOne(ReviewExt::class, 'imageable');
        });
        Rating::resolveRelationUsing('helpful', function ($reviewExtModel) {
            return $reviewExtModel->hasMany(FoundHelpful::class);
        });
        Schema::defaultStringLength(191);
        Mechanic::observe(MechanicObserver::class);
        AdService::observe(AdServiceObserver::class);
        Rating::observe(RatingObserver::class);
    }
}
