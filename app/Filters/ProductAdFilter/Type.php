<?php
namespace App\Filters\ProductAdFilter;

use App\Filters\BaseFilter;

class Type extends BaseFilter {

    protected function applyFilter($builder)
    {
        $filter = request($this->filterName());
        return $builder->whereHas($this->filterName(), function($query) use($filter) {
            $query->where('name', $filter);
        });
    }
}