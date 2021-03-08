<?php
namespace App\Filters\Shop;

use App\Filters\Shop\BaseFilter;

class UserType extends BaseFilter {

    protected function applyFilter($builder)
    {
        $filter = request($this->filterName());
        return $builder->where('user_type', $filter);
    }
}