<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Mechanic\MechanicResource;
use App\Http\Resources\PartDealer\PartDealercResource;

class UserResourceCollection extends JsonResource
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
            'user_info' => [
                'id' => $this->encodedKey,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email'=> $this->email,
                'verified' => is_null($this->email_verified_at) ? 'no' : 'yes',
                'permissions' => $this->getPermissionNames()
            ],
            'mechanic' => new MechanicResource($this->mechanic),
            'part_dealer' => new PartDealercResource($this->partDealer)
        ];
    }

}
