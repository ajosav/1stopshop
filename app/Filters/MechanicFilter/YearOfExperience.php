<?php

namespace App\Http\MechanicFilter;

use App\Filters\BaseFilter;

class YearOfExperience extends BaseFilter {

    protected function applyFilter($builder)
    {
        return $builder->whereHas('mechanic', function($mechanic) {
            $experience = request($this->filterName());
            $mechanic->where('experience_years', $experience);
        });
    }
}