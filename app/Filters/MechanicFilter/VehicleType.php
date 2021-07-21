<?php
namespace App\Filters\MechanicFilter;

use App\Filters\BaseFilter;

class VehicleType extends BaseFilter {

    protected function applyFilter($builder)
    {
        return $builder->where(function($query) {
            $vehcile_type = request($this->filterName());
            return $query->where($this->filterName(), 'like', '%' . $vehcile_type . '%');
        });
        
    }
}