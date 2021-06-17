<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\User\UserResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResourceCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
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
            'price'             =>          number_format($this->price, 2),
            'negotiable'        =>          $this->negotiable,
            'product_no'        =>          $this->product_no,
            'product_type'      =>          $this->product_type,
            'status'            =>          $this->status,
            'date_created'      =>          $this->created_at->format('Y-m-d H:i:s'),
            'created_by'        =>          new UserResourceCollection($this->user),
            'category'          =>          $this->category_name,
            'sub_category'      =>          $this->sub_category_name,
            'views'             =>          $total_views->groupBy('request_ip')->count(),
            'mobile_views'      =>          $total_views->where('mobile_view', 1)->groupBy('request_ip')->count(),
            'desktop_views'     =>          $total_views->where('desktop_view', 1)->groupBy('request_ip')->count(),
            'viewed_contact'    =>          $this->userViewContact->count()
        ];
    }
}
