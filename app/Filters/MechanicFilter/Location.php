<?php
namespace App\Filters\MechanicFilter;

use App\Filters\BaseFilter;

class Location extends BaseFilter {

    protected function applyFilter($builder)
    {
        return $builder->whereHas('mechanic', function($mechanic) {
            $location = request($this->filterName());
            $mechanic->where('city', 'like', '%' . $location . '%')
            ->orWhere('state', 'like', '%' . $location . '%')
            ->orWhere('office_address', 'like', '%' . $location . '%');
        });
    }
}