<?php
namespace App\Filters\ProductAdFilter;

use App\Filters\BaseFilter;

class Category extends BaseFilter {

    protected function applyFilter($builder)
    {
        $filter = request($this->filterName());
        return $builder->where(function($query) use ($filter) {
            return $query->where('category_name', $filter)
                        ->orWhere('sub_category_name', $filter);
        });
    }
}