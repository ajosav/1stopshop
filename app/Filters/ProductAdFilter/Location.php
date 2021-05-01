<?php
namespace App\Filters\ProductAdFilter;

use App\Filters\BaseFilter;

class Location extends BaseFilter {

    protected function applyFilter($builder)
    {
        return $builder->whereHas('user', function($query) {
            $query->whereHas('partDealer', function($company) {
                $location = request($this->filterName());
                return $company->where('city', 'like', '%' . $location . '%')
                        ->orWhere('state', 'like', '%' . $location . '%')
                        ->orWhere('office_address', 'like', '%' . $location . '%');
            });
        });
    }
}