<?php

namespace App\Observers;

use App\Models\AdService;

class AdServiceObserver
{
    /**
     * Handle the AdService "created" event.
     *
     * @param  \App\Models\AdService  $AdService
     * @return void
     */
    public function created(AdService $AdService)
    {
        //
    }

    /**
     * Handle the AdService "updated" event.
     *
     * @param  \App\Models\AdService  $AdService
     * @return void
     */
    public function updated(AdService $AdService)
    {
        //
    }

    /**
     * Handle the AdService "deleted" event.
     *
     * @param  \App\Models\AdService  $AdService
     * @return void
     */
    public function deleted(AdService $adService)
    {
        $adService->abuses()->delete();
        $adService->productViews()->delete();
        $adService->userViewContact()->delete();;
        $adService->notifications()->delete();
        $adService->ratings()->delete();
    }

    /**
     * Handle the AdService "restored" event.
     *
     * @param  \App\Models\AdService  $AdService
     * @return void
     */
    public function restored(AdService $AdService)
    {
        //
    }

    /**
     * Handle the AdService "force deleted" event.
     *
     * @param  \App\Models\AdService  $AdService
     * @return void
     */
    public function forceDeleted(AdService $AdService)
    {
        //
    }
}
