<?php

namespace App\Filters\MechanicFilter;

use App\Filters\BaseFilter;

class YearOfExperience extends BaseFilter {

    protected function applyFilter($builder)
    {
        $experience = request($this->filterName());
        return $builder->where('experience_years', $experience);
    }
}