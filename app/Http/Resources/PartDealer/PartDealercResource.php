<?php

namespace App\Http\Resources\PartDealer;

use Illuminate\Http\Resources\Json\JsonResource;

class PartDealercResource extends JsonResource
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
            'id'                            =>          $this->encodedKey,
            'phone_number'                  =>          $this->phone_number,
            'tax_identification_no'         =>          $this->tax_identification_no,
            'identification_type'           =>          $this->identification_type,
            'identity_number'               =>          $this->identity_number,
            'office_address'                =>          $this->office_address,
            'shop_name'                     =>          $this->shop_name,
            'shop_description'              =>          $this->shop_description,
            'state'                         =>          $this->state,
            'city'                          =>          $this->city,
            'company_photo'                 =>          encodePhoto($this->company_photo),
            'created_at'                    =>          $this->created_at
        ];
    }
}
