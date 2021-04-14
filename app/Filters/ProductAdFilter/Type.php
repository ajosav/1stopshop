<?php
namespace App\Filters\ProductAdFilter;

use App\Filters\BaseFilter;

class Type extends BaseFilter {

    protected function applyFilter($builder)
    {
        $filter = request($this->filterName());
        return $builder->where('ad_product_type', $filter);
        // return $builder;
    }
}