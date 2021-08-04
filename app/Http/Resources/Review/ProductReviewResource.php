<?php

namespace App\Http\Resources\Review;

use App\Models\ReviewExt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // $review = ReviewExt::where('imageable_id', $this->id)->first();
        $review = $this->reviewExt;

        if($review->review_photo) {
            
            $photos = [];        

            $review_photo = json_decode($review->review_photo);
            foreach($review_photo as $photo){
                $photos[] = asset(Storage::url($photo)); 
            }
        } else {
            $photos = "";
        }

        $helpful = $this->helpful()->select('user_id')->get()->map(function($key) {
            return $key['user_id'];
        });

        return [
            "id"                    =>  $this->id,
            "overall_rating"        =>  $this->rating,
            "durability"            =>  $this->customer_service_rating,
            "quality"               =>  $this->quality_rating,
            "value_for_money"       =>  $this->friendly_rating,
            "headline"              =>  $this->title,
            "written_review"        =>  $this->body,
            "date_created"          =>  $this->created_at->format('Y-m-d H:i:s a'),
            "display_name"          =>  $review->display_name,
            "display_photo"         =>  is_null($review->owner_photo) || $review->owner_photo == "" ? "" : asset(Storage::url($review->owner_photo)),
            "review_photo"          =>  $photos,
            "found_helpful"         =>  $helpful
        ];
    }
}
