<?php
namespace App\Filters\ProductAdFilter;

use App\Filters\BaseFilter;

class ProductNo extends BaseFilter {

    protected function applyFilter($builder)
    {
        $filter = request($this->filterName());
        return $builder->where($this->filterName(), 'like', '%' . $filter . '%');
    }
}