<?php

namespace App\Http\Resources\Review;

use App\Models\ReviewExt;
use Illuminate\Support\Facades\Storage;
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
        $review = ReviewExt::where('imageable_id', $this->id)->first();

        if($review->review_photo) {
            
            $photos = [];        

            $review_photo = json_decode($review->review_photo);
            foreach($review_photo as $photo){
                $photos[] = asset(Storage::url($photo)); 
            }
        } else {
            $photos = "";
        }

        return [
            "overall_rating" => $this->rating,
            "professionalism" => $this->customer_service_rating,
            "experience" => $this->quality_rating,
            "response_to_time" => $this->friendly_rating,
            "headline" => $this->title,
            "written_review" => $this->body,
            "date_created" => $this->created_at->format('Y-m-d H:i:s a'),
            "display_name" => $review->display_name,
            "display_photo" => asset(Storage::url($review->owner_photo)),
            "review_photo" =>  $photos,
        ];
    }
}
