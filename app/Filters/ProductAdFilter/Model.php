<?php
namespace App\Filters\ProductAdFilter;

use App\Filters\BaseFilter;

class Model extends BaseFilter {

    protected function applyFilter($builder)
    {
        $filter = request($this->filterName());
        return $builder->where($this->filterName(), $filter);
    }
}