<?php
namespace App\Filters\ProductAdFilter;

use App\Filters\BaseFilter;

class Search extends BaseFilter {
    public $search;
    public function __construct()
    {
        $this->search = request($this->filterName());
    }
    protected function applyFilter($builder)
    {
        return $builder->where(function($query) {
                    return $query->where('product_title', 'like', '%' . $this->search . '%')
                            ->orWhere('make', 'like', '%' . $this->search . '%')
                            ->orWhere('model', 'like', '%' . $this->search . '%')
                            ->orWhere('keyword', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('user', function($query) {
                    $query->whereHas('mechanic', function($mechanic) {
                        return $mechanic->where('city', 'like', '%' . $this->search . '%')
                                ->orWhere('state', 'like', '%' . $this->search . '%')
                                ->orWhere('office_address', 'like', '%' . $this->search . '%')
                                ->orWhere(function($filter) {
                                    $filter->where('specialization', 'like', '%' . $this->search . '%')
                                        ->orWhere('service_area', 'like', '%' . $this->search . '%');
                                });
                    })->orWhereHas('partDealer', function($part_dealer){
                        return $part_dealer->where('city', 'like', '%' . $this->search . '%')
                                ->orWhere('state', 'like', '%' . $this->search . '%')
                                ->orWhere('office_address', 'like', '%' . $this->search . '%');
                    });
                });
    }
}