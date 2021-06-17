<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $total_views = $this->productViews;
        return [
            'id'                =>          $this->encodedKey,
            'product_title'     =>          $this->product_title,
            'keyword'           =>          $this->keyword,
            'condition'         =>          $this->condition,
            'year'              =>          $this->year,
            'make'              =>          $this->make,
            'model'             =>          $this->model,
            'warranty'          =>          $this->warranty,
            'product_photo'     =>          getPhotoEncodedPhoto($this->product_photo),
            'description'       =>          $this->description,
            'price'             =>          $this->price,
            'negotiable'        =>          $this->negotiable,
            'product_no'        =>          $this->product_no,
            'product_type'      =>          $this->product_type,
            'status'            =>          $this->status,
            'date_created'      =>          $this->created_at->format('Y-m-d H:i:s'),
            'views'             =>          $total_views->groupBy('request_ip')->count(),
            'mobile_views'      =>          $total_views->where('mobile_view', 1)->groupBy('request_ip')->count(),
            'desktop_views'     =>          $total_views->where('desktop_view', 1)->groupBy('request_ip')->count(),
            'viewed_contact'    =>          $this->userViewContact->count(),
            "customer_reviews"  =>          $this->customerReviews(),
        ];
    }
}
