<?php

namespace App\Http\Resources\Mechanic;

use Illuminate\Http\Resources\Json\JsonResource;

class MechanicResource extends JsonResource
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
            'professional_skill'            =>          $this->professional_skill,
            'specialization'                =>          $this->specialization,
            'experience_years'              =>          $this->experience_years,
            'service_area'                  =>          $this->service_area,
            'office_address'                =>          $this->office_address,
            'state'                         =>          $this->state,
            'city'                          =>          $this->city,
            'company_photo'                 =>          $this->company_photo,
            'working_hours'                 =>          $this->working_hours
        ];
    }
}