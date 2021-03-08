<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResourceCollection extends JsonResource
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
            'id'                            =>          $this->encodedKey,
            'phone_number'                  =>          $this->phone_number,
            'profile_photo'                 =>          $this->profile_photo,
            'tax_identification_no'         =>          $this->tax_identification_no,
            'identification_type'           =>          $this->identification_type,
            'identity_number'               =>          $this->identity_number,
            'professional_skill'            =>          $this->professional_skill,
            'specialization'                =>          $this->specialization,
            'experience_years'              =>          $this->experience_years,
            'service_area'                  =>          $this->service_area,
            'isVerified'                    =>          $this->isVerified == 0 ? 'no' : 'yes',
        ];
    }
}
