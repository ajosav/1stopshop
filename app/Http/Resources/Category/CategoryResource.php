<?php

namespace App\Http\Resources\Category;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // dd($this->subCategories);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sub_categories' => $this->subCategories
        ];
    }
}
