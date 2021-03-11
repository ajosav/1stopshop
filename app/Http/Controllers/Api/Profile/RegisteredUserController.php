<?php

namespace App\Http\Controllers\Api\Profile;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Traits\GetRequestType;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResourceCollection;
use App\Services\Shop\GeneralShopService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RegisteredUserController extends Controller
{
    use GetRequestType;

    public $shopService, $userService;
  
    public function __construct(GeneralShopService $shopService, UserService $userService)
    {
        $this->shopService = $shopService;
        $this->userService = $userService;
    }
    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $all_users = $this->getUserDetail(
            $this->userService->getAllUsers()
        );
       
        return $all_users->additional([
            'message' => 'All registered users retrieved successfully',
            'status' => "success"
        ]);
    }

    public function getAllVerifiedVendors() {
        $all_users = $this->getUserDetail(
            $this->userService->getVerifiedUsers()
        );
        return $all_users->additional([
            'message' => 'Verifed users retrieved successfully',
            'status' => "success"
        ]);
    }

    public function findUser($encodedKey) {
        $user  = User::where('encodedKey', $encodedKey);
        $user_detail = $this->getSingleUser($user);

        return response()->success("User information retrieved successfully", $user_detail);
    }

    public function findUserByType($user_type) {
        $user  = User::where('user_type', $user_type);
        $all_mechanics = $this->getUserDetail($user);
        
        if(count($all_mechanics) < 1) {
            throw (new ModelNotFoundException)->setModel(
                new User
            );
        }

        return $all_mechanics->additional([
            'message' => $user_type . 's retrieved successfully',
            'status' => "success"
        ]);

    }
}
