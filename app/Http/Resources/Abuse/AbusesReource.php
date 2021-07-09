<?php

namespace App\Http\Resources\Abuse;

use Illuminate\Http\Resources\Json\JsonResource;

class AbusesReource extends JsonResource
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
            'full_name' => $this->full_name,
            'email' => $this->email,
            'message' => $this->message,
            'created_date' => $this->created_at->diffForHumans()
        ];
    }
}
