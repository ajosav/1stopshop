<?php

namespace App\Http\Resources\Review;

use App\Models\ReviewExt;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Review\HelpfulResource;
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
        // $review = ReviewExt::where('imageable_id', $this->id)->first();

        $review = $this->reviewExt;
        
        if(!is_null($review) && $review->review_photo) {
            
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
        $resources = [
            "id"                    => $this->id,
            "overall_rating"        => $this->rating,
            "professionalism"       => $this->customer_service_rating,
            "experience"            => $this->quality_rating,
            "response_to_time"      => $this->friendly_rating,
            "headline"              => $this->title,
            "written_review"        => $this->body,
            "date_created"          => $this->created_at->format('Y-m-d H:i:s a'),
            "display_name"          => is_null($review) ? "" : $review->display_name,
            "display_photo"         => is_null($review) || $review->owner_photo == "" ? "" : asset(Storage::url($review->owner_photo)),
            "review_photo"          => $photos,
            "found_helpful"         => $helpful
        ];

        if(request()->fullUrl() === route('admin.services-reviews')) {

            $resources["mechanic_link"] = is_null($this->reviewrateable) ? '' : route('mechanic.show', $this->reviewrateable->user->encodedKey)."?fullDetails=true";
        }

        return $resources;
    }
}
