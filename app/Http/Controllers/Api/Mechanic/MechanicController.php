<?php

namespace App\Http\Controllers\Api\Mechanic;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Mechanic;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Traits\GetRequestType;
use App\Services\MechanicService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Http\Requests\Appointment\OffDayRequest;
use App\Http\Requests\Auth\CreateMechanicRequest;
use App\Http\Requests\Auth\UpdateMechanicDetails;
use App\Http\Resources\User\UserResourceCollection;
use App\Http\Resources\WorkHours\WorkHoursResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MechanicController extends Controller
{
    use GetRequestType;

    public $mechanicService;
    
    public function __construct(MechanicService $mechanicService)
    {
        $this->middleware('auth.jwt')->except('index', 'shows', 'filterService', 'show', 'getWorkingHours');
        $this->mechanicService = $mechanicService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mechanic = $this->mechanicService->getVerifiedMechanics();

        $verified_mechs = $mechanic->with(['mechanic' => function($query) {
                return $query->select('mechanics.*', DB::raw('ROUND(AVG(rating), 2) as averageReviewRateable,count(rating) as countReviewRateable,ROUND(AVG(customer_service_rating), 2) as averageCustomerServiceReviewRateable,ROUND(AVG(quality_rating), 2) as averageQualityReviewRateable,ROUND(AVG(friendly_rating), 2) as averageFriendlyReviewRateable'
            ))
            ->leftJoin('reviews', function($join) {
                $join->on('reviews.reviewrateable_id', 'mechanics.id')
                ->on('reviews.reviewrateable_type', DB::raw("'App\\\Models\\\Mechanic'"));
            })
            ->leftJoin('notifications', function($join) {
                $join->on('notifications.author_id', 'mechanics.id')
                ->on('notifications.author_type', DB::raw("'App\\\Models\\\Mechanic'"))
                ->on('notifications.status',  DB::raw("'unread'"));
            })
            ->join('users', function($join)  {
                $join->on('users.id', 'mechanics.user_id');
            })
            ->groupBy('mechanics.id');
        }, 'partDealer', 'permissions']);

        if(request()->has('fullDetails') && request('fullDetails') === 'true') {
            $response = UserResourceCollection::collection($verified_mechs->paginate(20));
        } else {
            $response = UserResource::collection($verified_mechs->paginate(20));
        }
        
        return $response->additional([
            'message' => 'All verified mechanics retrieved successfully',
            'status' => "success"
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateMechanicRequest $request)
    {
        $user = auth('api')->user();
        return $this->mechanicService->createNewMechanic(Arr::except($request->validated(), 'no_tax_id'), $user);
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
                    ->join('mechanics', 'mechanics.user_id', 'users.id')
                    ->where('users.encodedKey', $encodedKey);

        if(request()->has('fullDetails') && request('fullDetails') === 'true') {
            $new_user = $user->with(['mechanic' => function($query) {
                return $query->select('mechanics.*', DB::raw('
                    ROUND(AVG(rating), 2) as averageReviewRateable, 
                    count(rating) as countReviewRateable,
                    ROUND(AVG(customer_service_rating), 2) as averageCustomerServiceReviewRateable,
                    ROUND(AVG(quality_rating), 2) as averageQualityReviewRateable, 
                    ROUND(AVG(friendly_rating), 2) as averageFriendlyReviewRateable'
                ))
                ->leftJoin('reviews', function($join) {
                    $join->on('reviews.reviewrateable_id', 'mechanics.id')
                    ->on('reviews.reviewrateable_type', DB::raw("'App\\\Models\\\Mechanic'"));
                })
                ->groupBy('mechanics.id');
            }, 'partDealer', 'permissions'])->firstOrFail();
            $found_user = new UserResourceCollection($new_user);
        } else {
            $found_user = new UserResource($user->firstOrFail());
        }
        

        return response()->success("User information retrieved successfully", $found_user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getWorkingHours(Mechanic $mechanic)
    {
        return $this->mechanicService->getMechanicSchedule($mechanic);

        // return response()->success("Mechanic information retrieved successfully", $all_mechanics);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function filterService()
    {
        $user = $this->mechanicService->filterMechanicServices();
        $all_mechanics =  $user->with(['mechanic' => function($query) {
            return $query->select('mechanics.*', DB::raw('
                ROUND(AVG(rating), 2) as averageReviewRateable,
                count(rating) as countReviewRateable,
                ROUND(AVG(customer_service_rating), 2) as averageCustomerServiceReviewRateable,
                ROUND(AVG(quality_rating), 2) as averageQualityReviewRateable,
                ROUND(AVG(friendly_rating), 2) as averageFriendlyReviewRateable'
            ))
            ->leftJoin('reviews', function($join) {
                $join->on('reviews.reviewrateable_id', 'mechanics.id')
                ->on('reviews.reviewrateable_type', DB::raw("'App\\\Models\\\Mechanic'"));
            })
            ->leftJoin('notifications', function($join) {
                $join->on('notifications.author_id', 'mechanics.id')
                ->on('notifications.author_type', DB::raw("'App\\\Models\\\Mechanic'"))
                ->on('notifications.status',  DB::raw("'unread'"));
            })
            ->join('users', function($join)  {
                $join->on('users.id', 'mechanics.user_id');
            })
            ->groupBy('mechanics.id');
        }, 'partDealer', 'permissions'])->paginate(20);
        
        return UserResourceCollection::collection($all_mechanics)->additional([
            'message' => 'Mechanic Details filtered successfully',
            'status' => "success"
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CreateMechanicRequest $request)
    {
        $user = auth('api')->user();
        return $this->mechanicService->updateMechanicData(Arr::except($request->validated(), 'no_tax_id'), $user);
    }

    public function editSchedule(Request $request) {
        // dd("Hello");
        $schedule = $request->validate([
            "schedule"                              =>  "required|array",  
            "schedule.*"                            =>  "required|array",  
            "schedule.*.*"                          =>  "required|array",  
            "schedule.*.*.hour"                     =>  "required|numeric|min:1|max:12",
            "schedule.*.*.meridian"                 =>  "required|in:AM,PM",
            "schedule.*.*.isActive"                 =>  "required|in:true,false",
            "schedule_data"                         =>  "required|array"
        ]);
        return $this->mechanicService->editMechanicSchedule($schedule);
    }

    public function updateOffDaySchedule(OffDayRequest $request) {
        return $this->mechanicService->updateOffDaySchedule($request->validated());
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
