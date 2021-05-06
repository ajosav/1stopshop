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

        foreach($working_hours as $day => $value) {
            $from_meridian = $value['from']['meridian'];
            $from_hour = $value['from']['hour'];
            $to_meridian = $value['to']['meridian'];
            $to_hour = $value['to']['hour'];

            if($value['from']['meridian'] == "PM") {
                if($value['from']['hour'] != 12) {
                    $from_hour = (int) $value['from']['hour'] + 12;
                }
            }
            if($value['to']['meridian'] == "PM") {
                if($value['to']['hour'] != 12) {
                    $to_hour = (int) $value['to']['hour'] + 12;
                }
            }

            if($value['to']['meridian'] == "AM" && $value['to']['hour'] == 12){
                $to_hour = 00;
            }
            if($value['from']['meridian'] == "AM" && $value['from']['hour'] == 12){
                $from_hour = 00;
            }
            
            $mechanic->workingHours()->create([
                "day" => $day,
                "from_hour" => $from_hour,
                "from_meridian" => $from_meridian,
                "to_hour" => $to_hour,
                "to_meridian" => $to_meridian
            ]);
            
        }
    }

    /**
     * Handle the Mechanic "updated" event.
     *
     * @param  \App\Models\Mechanic  $mechanic
     * @return void
     */
    public function updated(Mechanic $mechanic)
    {
        //
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
