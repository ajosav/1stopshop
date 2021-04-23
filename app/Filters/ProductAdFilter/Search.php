<?php
namespace App\Filters\ProductAdFilter;

use App\Filters\BaseFilter;

class Search extends BaseFilter {

    protected function applyFilter($builder)
    {
        return $builder->whereHas('user', function($query) {
            $query->whereHas('mechanic', function($mechanic) {
                $location = request($this->filterName());
                return $mechanic->where('city', 'like', '%' . $location . '%')
                        ->orWhere('state', 'like', '%' . $location . '%')
                        ->orWhere('office_address', 'like', '%' . $location . '%')
                        ->orWhere(function($filter) {
                            $search = request($this->filterName());
                            $filter->where('specialization', 'like', '%' . $search . '%')
                                ->orWhere('service_area', 'like', '%' . $search . '%');
                        });
            })->orWhereHas('partDealer', function($part_dealer){
                $search = request($this->filterName());
                return $part_dealer->where('city', 'like', '%' . $search . '%')
                        ->orWhere('state', 'like', '%' . $search . '%')
                        ->orWhere('office_address', 'like', '%' . $search . '%');
            });
        });
    }
}