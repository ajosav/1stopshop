<?php
namespace App\Filters\ProductAdFilter;

use App\Filters\BaseFilter;

class Location extends BaseFilter {

    protected function applyFilter($builder)
    {
        return $builder->whereHas('user', function($query) {
            $query->whereHas('company', function($company) {
                $location = request($this->filterName());
                $company->where('city', 'like', '%' . $location . '%')
                        ->orWhere('state', 'like', '%' . $location . '%')
                        ->orWhere('street_name', 'like', '%' . $location . '%');
            });
        });
    }
}