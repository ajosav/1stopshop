<?php

namespace App\Http\Controllers\Api\Mechanic;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\GetRequestType;
use App\Services\MechanicService;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MechanicController extends Controller
{
    use GetRequestType;

    public $mechanicService;
    
    public function __construct(MechanicService $mechanicService)
    {
        $this->middleware('auth.jwt')->except('index', 'shows');
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($encodedKey)
    {
        $user  = User::where('encodedKey', $encodedKey)->where('user_type', 'mechanic');
        $all_mechanics = $this->getSingleUser($user);

        return response()->success("User information retrieved successfully", $all_mechanics);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
