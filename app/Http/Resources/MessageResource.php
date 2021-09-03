<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'full_name'     => $this->full_name,
            'email'         => $this->email,
            'message'       => $this->message,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at
        ];
    }
}
