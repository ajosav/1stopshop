<?php

namespace App\Http\Controllers\Api\Review;

use App\Helpers\ShopDataHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Review\RateMechanicRequest;
use App\Http\Resources\Review\UserReviewResource;
use App\Models\Mechanic;
use App\Models\ReviewExt;
use App\Models\User;
use Codebyray\ReviewRateable\Models\Rating;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware('auth.jwt');   
        $this->user = auth('api')->user();
    }

    public function rateMechanic(RateMechanicRequest $request, User $mechanic) {
        $mechanic = $mechanic->mechanic;
        if(!$user_rated = $this->user->ratings()->where('reviewrateable_id', $mechanic->id)->first()) {
            $rating = $mechanic->rating([
                'title' => $request->headline,
                'body' => $request->written_review,
                'customer_service_rating' => $request->professionalism,  //professionalism
                'quality_rating' => $request->experience,               //experience
                'friendly_rating' => $request->response_to_time,        //response to time
                'rating' => $request->overall_rating,                   //overall rating
                'recommend' => 'Yes',
                'approved' => true, // This is optional and defaults to false
            ], $this->user);
        } else {
            $rating = $mechanic->updateRating($user_rated->id, [
                'title' => $request->headline,
                'body' => $request->written_review,
                'customer_service_rating' => $request->professionalism,  //professionalism
                'quality_rating' => $request->experience,               //experience
                'friendly_rating' => $request->response_to_time,        //response to time
                'rating' => $request->overall_rating,                   //overall rating
                'recommend' => 'Yes',
                'approved' => true, // This is optional and defaults to false
            ], $this->user);
        }

        $data = ShopDataHelper::createReviewPhotoData($request->validated());

        $review_photos = [];
        if($request->review_photo) {
            foreach($request->review_photo as $photo) {
                $review_photos[] = uploadImage('images/reviews/', $photo);
            }
        }
        ReviewExt::updateOrCreate([
            'imageable_id' => $rating->id,
        ],
        [
            'display_name' => $request->display_name,
            'review_photo' => json_encode($review_photos),
            'imageable_type' => 'Codebyray\ReviewRateable\Models\Rating'
        ]);
        
        return (new UserReviewResource($rating))->additional([
            'message' => 'Your ratings has been submitted successfully',
            'status' => 'success'
        ]);

    }

    public function mechanicReviews(User $mechanic) {
        $mechanic = $mechanic->mechanic;
        $ratings = $mechanic->getAllRatings($mechanic->id, 'desc');
        
        return (UserReviewResource::collection($ratings))->additional([
            'message' => 'Mechanic reviews retrieved successfully',
            'status' => 'success'
        ]);
    }

    public function userReview(User $mechanic) {
        $mechanic = $mechanic->mechanic;
        $user_rated = $this->user->ratings()->where('reviewrateable_id', $mechanic->id)->first();

        if(!$user_rated) {
            return response()->errorResponse("No review found", [], 404);
        }

        return (new UserReviewResource($user_rated))->additional([
            'message' => 'Rating retrieved successfully',
            'status' => 'success'
        ]);
    } 

    // public function 
}
