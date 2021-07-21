<?php
namespace App\Filters\MechanicFilter;

use App\Filters\BaseFilter;

class ProfessionalSkill extends BaseFilter {

    protected function applyFilter($builder)
    {
        return $builder->where(function($query) {
            $profession = request($this->filterName());
            return $query->whereNotNull($this->filterName())->where($this->filterName(), 'like', '%' . $profession . '%');
        });
    }
}