<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the user "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        // 
    }

    /**
     * Handle the user "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        //
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        $user->partDealer()->delete();
        $user->mechanic()->delete(); 
    }

    /**
     * Handle the user "restored" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        $user->partDealer()->withThrashed()->restore();
        $user->mechanic()->withThrashed()->restore(); 
    }

    /**
     * Handle the user "force deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        $user->partDealer()->forceDelete();
        $user->mechanic()->forceDelete();        
        $user->adService()->delete();
        $user->ratings()->delete();
        $user->userAppointment()->delete();
        $user->notifications()->delete();
    }
}
