<?php

namespace App\Http\Controllers\Api\PartDealer;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Traits\GetRequestType;
use App\Services\PartDealerService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CreatePartDealerRequest;

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
        $user  = User::where('encodedKey', $encodedKey)->whereHas('permissions', function($query) {
            return $query->whereName('part dealer');
        });
        $all_mechanics = $this->getSingleUser($user);

        return response()->success("User information retrieved successfully", $all_mechanics);
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
