<?php

namespace App\Observers;

use App\Models\Appointment\WorkingHour;
use App\Models\Mechanic;

class MechanicObserver
{
    /**
     * Handle the Mechanic "created" event.
     *
     * @param  \App\Models\Mechanic  $mechanic
     * @return void
     */
    public function created(Mechanic $mechanic)
    {
        $working_hours = $mechanic->working_hours;

        addWorkingHours($working_hours, $mechanic);
    }

    /**
     * Handle the Mechanic "updated" event.
     *
     * @param  \App\Models\Mechanic  $mechanic
     * @return void
     */
    public function updated(Mechanic $mechanic)
    {
        $working_hours = $mechanic->working_hours;

        $mechanic->workingHours()->delete();
    
        addWorkingHours($working_hours, $mechanic);
    }

    /**
     * Handle the Mechanic "deleted" event.
     *
     * @param  \App\Models\Mechanic  $mechanic
     * @return void
     */
    public function deleted(Mechanic $mechanic)
    {
        //
    }

    /**
     * Handle the Mechanic "restored" event.
     *
     * @param  \App\Models\Mechanic  $mechanic
     * @return void
     */
    public function restored(Mechanic $mechanic)
    {
        //
    }

    /**
     * Handle the Mechanic "force deleted" event.
     *
     * @param  \App\Models\Mechanic  $mechanic
     * @return void
     */
    public function forceDeleted(Mechanic $mechanic)
    {
        //
    }
}
