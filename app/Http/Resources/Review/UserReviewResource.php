<?php

namespace App\Http\Resources\Review;

use Illuminate\Http\Resources\Json\JsonResource;

class UserReviewResource extends JsonResource
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
            "overall_rating" => $this->rating,
            "professionalism" => $this->customer_service_rating,
            "experience" => $this->quality_rating,
            "response_to_time" => $this->friendly_rating,
            "headline" => $this->title,
            "written_review" => $this->body,
            "date_created" => $this->created_at->format('Y-m-d H:i:s a')
        ];
    }
}
