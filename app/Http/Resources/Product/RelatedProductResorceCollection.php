<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\User\UserResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class RelatedProductResorceCollection extends JsonResource
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
            'product_photo'     =>          $this->product_photo,
            'description'       =>          $this->description,
            'price'             =>          $this->price,
            'negotiable'        =>          $this->negotiable,
            'product_no'        =>          $this->product_no,
            'product_type'      =>          $this->product_type,
            'date_created'      =>          $this->created_at->format('Y-m-d H:i:s'),
            'created_by'        =>          new UserResourceCollection($this->user),
            'category'          =>          new CategoryResource($this->category),
            'related_products'  =>          ProductResourceCollection::collection($this->relatedProducts()->get()),
            'views'             =>          $this->productViews->count()
        ];
    }
}
