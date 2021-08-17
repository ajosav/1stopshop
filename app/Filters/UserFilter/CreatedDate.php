<?php

namespace App\Filters\UserFilter;

use App\Filters\BaseFilter;

class CreatedDate extends BaseFilter {

    public function applyFilter($builder) {
        return $builder->where(function($query) {
            $search = request($this->filterName());
            return $query->whereDate('created_at', $search);
        });
    }
}