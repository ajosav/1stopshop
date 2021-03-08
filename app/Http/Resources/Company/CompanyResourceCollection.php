<?php

namespace App\Http\Resources\Company;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResourceCollection extends JsonResource
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
            'id'                    =>      $this->encodedKey,
            'office_no'             =>      $this->office_no,
            'street_name'           =>      $this->street_name,
            'city'                  =>      $this->city,
            'state'                 =>      $this->state,
            'region'                =>      $this->region,
            'country'               =>      $this->country,
            'company_mission'       =>      $this->company_mission,
            'shop_photo'            =>      $this->shop_photo
        ];
    }
}
