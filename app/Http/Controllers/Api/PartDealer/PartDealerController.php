<?php

namespace App\Http\Controllers\Api\PartDealer;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Traits\GetRequestType;
use Illuminate\Support\Facades\DB;
use App\Services\PartDealerService;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Http\Requests\Auth\CreatePartDealerRequest;
use App\Http\Resources\User\UserResourceCollection;

class PartDealerController extends Controller
{
    use GetRequestType;

    public $part_dealer;

    public function __construct(PartDealerService $part_dealer)
    {
        $this->middleware('auth.jwt')->except('index', 'show');
        $this->part_dealer = $part_dealer;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $part_dealer = $this->getUserDetail(
            $this->part_dealer->getVerifiedPartDealers()
        );
        
        return $part_dealer->additional([
            'message' => 'All verified part dealers retrieved successfully',
            'status' => "success"
        ]);
    }

   
    public function store(CreatePartDealerRequest $request)
    {
        $user = auth('api')->user();
        return $this->part_dealer->createNewPartDealer(Arr::except($request->validated(), 'no_tax_id'), $user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($encodedKey)
    {
        $user  = User::select('users.*')
                    ->join('ad_services', 'ad_services.user_id', 'users.id')
                    ->where('users.encodedKey', $encodedKey);

        if(request()->has('fullDetails') && request('fullDetails') === 'true') {
            $new_user = $user->with(['mechanic', 'partDealer' => function($query) {
                return $query->select('ad_services.*', DB::raw('
                    ROUND(AVG(rating), 2) as averageReviewRateable, 
                    count(rating) as countReviewRateable,
                    ROUND(AVG(customer_service_rating), 2) as averageCustomerServiceReviewRateable,
                    ROUND(AVG(quality_rating), 2) as averageQualityReviewRateable, 
                    ROUND(AVG(friendly_rating), 2) as averageFriendlyReviewRateable'
                ))
                ->leftJoin('reviews', function($join) {
                    $join->on('reviews.reviewrateable_id', 'mechanics.id')
                    ->on('reviews.reviewrateable_type', DB::raw("'App\\\Models\\\AdService'"));
                })
                ->groupBy('mechanics.id');
            }, 'permissions'])->firstOrFail();
            $found_user = new UserResourceCollection($new_user);
        } else {
            $found_user = new UserResource($user->firstOrFail());
        }
        

        return response()->success("User information retrieved successfully", $found_user);
        
        // $user  = User::where('encodedKey', $encodedKey)->whereHas('permissions', function($query) {
        //     return $query->whereName('part_dealer');
        // });
        // $all_mechanics = $this->getSingleUser($user);

        // return response()->success("User information retrieved successfully", $all_mechanics);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CreatePartDealerRequest $request)
    {
        $user = auth('api')->user();
        return $this->part_dealer->updatePartDealer(Arr::except($request->validated(), 'no_tax_id'), $user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
