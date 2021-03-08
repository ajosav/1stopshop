<?php
namespace App\Filters\Shop;

use App\Filters\Shop\BaseFilter;

class Order extends BaseFilter {

    protected function applyFilter($builder)
    {
        $filter = request($this->filterName());
        if(in_array($filter, ['asc', 'desc'])) {
            return $builder->orderBy('created_at', $filter);
        }

        return $builder;
    }
}