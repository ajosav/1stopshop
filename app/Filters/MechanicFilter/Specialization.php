<?php
namespace App\Filters\MechanicFilter;

use App\Filters\BaseFilter;

class Specialization extends BaseFilter {

    protected function applyFilter($builder)
    {
        return $builder->where(function($query) {
            $profession = request($this->filterName());
            return $query->whereNotNull('professional_skill')->where('professional_skill', 'like', '%' . $profession . '%');
        });
    }
}