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
            return $query->where(function($query) {
                return $query->where('product_title', 'like', '%' . $this->search . '%')
                        ->orWhere('make', 'like', '%' . $this->search . '%')
                        ->orWhere('model', 'like', '%' . $this->search . '%')
                        ->orWhere('keyword', 'like', '%' . $this->search . '%')
                        ->orWhere('product_no', 'like', '%' . $this->search . '%');
                })->orWhereIn('ad_services.user_id', function($query) {
                    return $query->from('part_dealers')
                        ->select('user_id')
                        ->where('city', 'like', '%' . $this->search . '%')
                        ->orWhere('state', 'like', '%' . $this->search . '%')
                        ->orWhere('office_address', 'like', '%' . $this->search . '%');
                });
            });
    }
}