<?php

namespace App\Filters\UserFilter;

use App\Filters\BaseFilter;

class Search extends BaseFilter {

    public function applyFilter($builder) {
        return $builder->where(function($query) {
            $search = request($this->filterName());
            return $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
        });
    }
}