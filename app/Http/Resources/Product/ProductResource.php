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
        return [
            'id'                =>          $this->encodedKey,
            'product_title'     =>          $this->product_title,
            'keyword'           =>          $this->keyword,
            'condition'         =>          $this->condition,
            'year'              =>          $this->year,
            'make'              =>          $this->make,
            'model'             =>          $this->model,
            'warranty'          =>          $this->warranty,
            'product_photo'     =>          $this->product_photo,
            'description'       =>          $this->description,
            'price'             =>          $this->price,
            'negotiable'        =>          $this->negotiable,
            'product_no'        =>          $this->product_no,
            'product_type'      =>          $this->ad_product_type,
            'date_created'      =>          $this->created_at->format('Y-m-d H:i:s'),
            'views'             =>          $this->productViews->count()
        ];
    }
}
