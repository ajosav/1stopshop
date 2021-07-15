<?php

namespace App\Http\Resources\Feedback;

use Illuminate\Http\Resources\Json\JsonResource;

class FeedbackResource extends JsonResource
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
            "satisfaction" => $this->satisfaction,
            "recommendation" => $this->recommendation,
            "comment" => $this->comment,
            "email" => $this->email
        ];
    }
}
