<?php

namespace App\Http\Controllers\Api\Review;

use App\Models\User;
use App\Models\Mechanic;
use App\Models\AdService;
use App\Models\ReviewExt;
use Illuminate\Http\Request;
use App\Helpers\ShopDataHelper;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Codebyray\ReviewRateable\Models\Rating;
use App\Http\Requests\Review\RateProductRequest;
use App\Http\Requests\Review\RateMechanicRequest;
use App\Http\Resources\Review\UserReviewResource;
use App\Http\Resources\Review\ProductReviewResource;

class ReviewController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware('auth.jwt')->except(['mechanicReviews']);   
        $this->user = auth('api')->user();
    }

    public function rateMechanic(RateMechanicRequest $request, User $mechanic) {
        $mechanic = $mechanic->mechanic;
        if(!$user_rated = $this->user->ratings()->where('reviewrateable_id', $mechanic->id)->where('reviewrateable_type', 'App\Models\Mechanic')->first()) {
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
        $rating->reviewExt()->updateOrCreate([
            'imageable_id' => $rating->id,
        ],
        [
            'display_name' => $request->display_name,
            'review_photo' => json_encode($review_photos),
            'owner_photo' => $this->user->profile_image,
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

    public function rateProduct(RateProductRequest $request, AdService $adService){
        if(!$user_rated = $this->user->ratings()->where('reviewrateable_id', $adService->id)->where('reviewrateable_type', 'App\Models\Adservice')->first()) {
            $rating = $adService->rating([
                'title' => $request->headline,
                'body' => $request->written_review,
                'customer_service_rating' => $request->durability,  //durability
                'quality_rating' => $request->quality,               //quality
                'friendly_rating' => $request->value_for_money,        //value_for_money
                'rating' => $request->overall_rating,                   //overall rating
                'recommend' => 'Yes',
                'approved' => true, // This is optional and defaults to false
            ], $this->user);
        } else {
            $rating = $adService->updateRating($user_rated->id, [
                'title' => $request->headline,
                'body' => $request->written_review,
                'customer_service_rating' => $request->durability,      //durability
                'quality_rating' => $request->quality,                  //quality
                'friendly_rating' => $request->value_for_money,         //value_for_money
                'rating' => $request->overall_rating,                   //overall rating
                'recommend' => 'Yes',
                'approved' => true, // This is optional and defaults to false
            ], $this->user);
        }

        $review_photos = [];
        if($request->review_photo) {
            foreach($request->review_photo as $photo) {
                $review_photos[] = uploadImage('images/reviews/product', $photo);
            }
        }

        $rating->reviewExt()->updateOrCreate([
                'imageable_id' => $rating->id,
            ],
            [
                'display_name' => $request->display_name,
                'review_photo' => json_encode($review_photos),
                'owner_photo' => $this->user->profile_image,
                'imageable_type' => 'Codebyray\ReviewRateable\Models\Rating'
            ]);

        // ReviewExt::updateOrCreate([
        //     'imageable_id' => $rating->id,
        // ],
        // [
        //     'display_name' => $request->display_name,
        //     'review_photo' => json_encode($review_photos),
        //     'owner_photo' => $this->user->profile_image,
        //     'imageable_type' => 'Codebyray\ReviewRateable\Models\Rating'
        // ]);
        
        return (new ProductReviewResource($rating))->additional([
            'message' => 'Your ratings has been submitted successfully',
            'status' => 'success'
        ]);
        // return $adService;
    }
    
    public function productReviews(AdService $adService) {
        $ratings = $adService->getAllRatings($adService->id, 'desc');
        
        return (ProductReviewResource::collection($ratings))->additional([
            'message' => 'Product reviews retrieved successfully',
            'status' => 'success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function reportAbuse(Request $request, Rating $rating)
    {
        // return $rating;
        $abuse = $request->validate([
            'full_name' => 'required|string|max:155',
            'email' => 'required|email',
            'message' => 'required|string|min:3|max:300'
        ]);

        try {
            $rating->abuses()->create($abuse); 
        } catch(QueryException $e) {
            return response()->errorResponse('Failed to create abuse', ['errorSource' => $e->getMessage()]);
        }


        return response()->success('Abuse successfully submitted');
        
    }



    // public function 
}
