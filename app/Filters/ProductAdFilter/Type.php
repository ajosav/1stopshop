<?php
namespace App\Filters\ProductAdFilter;

use App\Filters\BaseFilter;

class Type extends BaseFilter {

    protected function applyFilter($builder)
    {
        $filter = request($this->filterName());

        return $builder->where('product_type', $filter);
    }
}