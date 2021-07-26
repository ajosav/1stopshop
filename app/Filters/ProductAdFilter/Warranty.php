<?php
namespace App\Filters\ProductAdFilter;

use App\Filters\BaseFilter;

class Warranty extends BaseFilter {

    protected function applyFilter($builder)
    {
        $filter = request($this->filterName());
        return $builder->where($this->filterName(), $filter);
    }
}