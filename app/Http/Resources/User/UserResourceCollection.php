<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Company\CompanyResourceCollection;


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
                'user_type'=> $this->user_type
            ],
            'profile' => new ProfileResourceCollection($this->userProfile),
            'company' => new CompanyResourceCollection($this->company)
        ];
    }

}
