<?php
namespace App\Filters\MechanicFilter;

use App\Filters\BaseFilter;

class ProfessionalSkill extends BaseFilter {

    protected function applyFilter($builder)
    {
        return $builder->whereHas('mechanic', function($mechanic) {
            $profession = request($this->filterName());
            return $mechanic->whereNotNul($this->filterName())->where($this->filterName(), 'like', '%' . $profession . '%');
        });
    }
}