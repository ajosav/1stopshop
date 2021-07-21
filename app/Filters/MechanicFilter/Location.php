<?php
namespace App\Filters\MechanicFilter;

use App\Filters\BaseFilter;

class Location extends BaseFilter {

    protected function applyFilter($builder)
    {   
        return $builder->where(function($query) {
            $location = request($this->filterName());
            return $query->where('mechanics.city', 'like', '%' . $location . '%')
                ->orWhere('mechanics.state', 'like', '%' . $location . '%')
                ->orWhere('mechanics.office_address', 'like', '%' . $location . '%');
        });

        // return $builder->join('part_dealers', 'part_dealers.user_id', 'ad_services.user_id')
        //     ->where('city', 'like', '%' . $location . '%')
        //     ->orWhere('state', 'like', '%' . $location . '%')
        //     ->orWhere('office_address', 'like', '%' . $location . '%');
    }
}