<?php

namespace App\Http\Resources\User;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'id' => $this->encodedKey,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email'=> $this->email,
            'profile_image' =>  asset(Storage::url($this->profile_image)),
            'verified' => is_null($this->email_verified_at) ? 'no' : 'yes',
            'permissions' => $this->getPermissionNames()
        ];
    }

}
