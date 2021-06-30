<?php

namespace App\Http\Resources\User;

use Illuminate\Support\Facades\Storage;
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
                'permissions' => $this->getPermissionNames(),
                'profile_image' =>  is_null($this->profile_image) || $this->profile_image == "" ? "" : asset(Storage::url($this->profile_image)),
                'phone_number' => $this->phone_number,
                'address' => $this->address,
                'state' => $this->state,
                'city' => $this->city,
                'created_at' => $this->created_at,
            ],
            'mechanic' => new MechanicResource($this->mechanic),
            'part_dealer' => new PartDealercResource($this->partDealer)
        ];
    }

}
