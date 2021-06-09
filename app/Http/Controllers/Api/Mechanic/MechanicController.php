<?php

namespace App\Http\Controllers\Api\Mechanic;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Traits\GetRequestType;
use App\Services\MechanicService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CreateMechanicRequest;
use App\Http\Requests\Auth\UpdateMechanicDetails;
use App\Http\Resources\User\UserResourceCollection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MechanicController extends Controller
{
    use GetRequestType;

    public $mechanicService;
    
    public function __construct(MechanicService $mechanicService)
    {
        $this->middleware('auth.jwt')->except('index', 'shows', 'filterService');
        $this->mechanicService = $mechanicService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $all_mechanics = $this->getUserDetail(
            $this->mechanicService->getVerifiedMechanics()
        );
        return $all_mechanics->additional([
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
        $user  = User::where('encodedKey', $encodedKey)->whereHas('permissions', function($query) {
            return $query->whereName('mechanic');
        });
        $all_mechanics = $this->getSingleUser($user);

        return response()->success("User information retrieved successfully", $all_mechanics);
    }


    // public function bookAppointment(Request $request, User $encodedKey) {
    //     $request->validate([
            
    //     ]);
    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function filterService()
    {
        $user = $this->mechanicService->filterMechanicServices();

        $all_mechanics =  $user->with('mechanic', 'partDealer')->jsonPaginate();
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
