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
            'created_by'        =>          new UserResourceCollection($this->user),
            'category'          =>          $this->category_name,
            'sub_category'      =>          $this->sub_category_name,
            'views'             =>          $this->productViews->count()
        ];
    }
}
