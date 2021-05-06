<?php
namespace App\Filters\MechanicFilter;

use App\Filters\BaseFilter;

class WorkingHours extends BaseFilter {

    protected function applyFilter($builder)
    {
        return $builder->whereHas('mechanic', function($mechanic) {
            return $mechanic->whereHas('workingHours', function($working_hour){
                $time_of_avail = request($this->filterName());
                if($time_of_avail == 'morning') {
                    return $working_hour->where('day', date("l"))
                    ->where(function($time) {
                        return $time->where('from_hour', '<=', 5)
                        ->orWhere('to_hour', '>', 12);
                    });
                } elseif($time_of_avail == 'afternoon') {
                    return $working_hour->where('day', date("l"))
                    ->where(function($time) {
                        return $time->where('from_hour', '<=', 12)
                            ->orWhere('to_hour', '>', 18);
                    });
                } elseif($time_of_avail == 'evening') {
                    return $working_hour->where('day', date("l"))
                    ->where(function($time) {
                        return $time->where('from_hour', '<=', 18)
                            ->orWhere('to_hour', '>', 24);
                    });
                } elseif($time_of_avail == 'night') {
                    return $working_hour->where('day', date("l"))
                    ->where(function($time) {
                        return $time->where('from_hour', '<=', 00)
                            ->orWhere('to_hour', '>', 5);
                    });
                }

            });
        });
    }
}