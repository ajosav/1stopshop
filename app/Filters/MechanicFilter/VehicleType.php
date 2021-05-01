<?php
namespace App\Filters\MechanicFilter;

use App\Filters\BaseFilter;

class VehicleType extends BaseFilter {

    protected function applyFilter($builder)
    {
        return $builder->whereHas('mechanic', function($mechanic) { 
            $vehcile_type = request($this->filterName());
            return $mechanic->where($this->filterName(), 'like', '%' . $vehcile_type . '%');
        });
    }
}