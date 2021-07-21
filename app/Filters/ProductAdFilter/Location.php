<?php
namespace App\Filters\ProductAdFilter;

use App\Filters\BaseFilter;

class Location extends BaseFilter {

    protected function applyFilter($builder)
    {
        
        $location = request($this->filterName());

        return $builder->join('part_dealers', 'part_dealers.user_id', 'ad_services.user_id')
            ->where('city', 'like', '%' . $location . '%')
            ->orWhere('state', 'like', '%' . $location . '%')
            ->orWhere('office_address', 'like', '%' . $location . '%');
        
        
        
        
        
        // $builder->whereHas('user', function($query) {
        //     $query->whereHas('partDealer', function($company) {
        //         $location = request($this->filterName());
        //         return $company->where('city', 'like', '%' . $location . '%')
        //                 ->orWhere('state', 'like', '%' . $location . '%')
        //                 ->orWhere('office_address', 'like', '%' . $location . '%');
        //     });
        // });
    }
}