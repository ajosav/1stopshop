<?php

namespace App\Http\Controllers\Api\Admin\Dashboard;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResourceCollection;
use App\Services\UserService;
use App\Traits\GetRequestType;
use Illuminate\Support\Facades\Cache;

class AdminDashBoardController extends Controller
{
    use GetRequestType;

    public function analytics() {
        $total_users = User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->get();

        return $total_users;
    }

    public function registeredUsers() {
        $daily_users = User::with(['mechanic', 'partDealer'])->whereDate('created_at', now())->get();

        return UserResourceCollection::collection($daily_users)->additional([
            'message' => 'Daily users retrieved successfully',
            'status' => "success"
        ]);

        // whereDate('created_at', now())->get();
        // $daily_users = User::leftJoin('mechanics', 'users.id', '=', 'mechanics.user_id')
        //                     ->leftJoin('part_dealers', 'users.id', '=', 'part_dealers.user_id')
        //                     ->orderBy('users.created_at', 'desc')
        //                     ->get();

    }

    public function getUsersByRoleCount() {
        $mechanics      = User::join('mechanics', 'users.id', '=', 'mechanics.user_id')->count();
        $part_dealers   = User::join('part_dealers', 'users.id', '=', 'part_dealers.user_id')->count();
        $users_count    = User::count();
        $regular_user   = User::doesntHave('mechanic')->doesntHave('partDealer')->count();

        return response()->success("Successfully returned users count", [
            'mechanics_count' => $mechanics,
            'part_dealers_count' => $part_dealers,
            'regular_user_count' => $regular_user,
            'total_users' => $users_count
        ]);
    }

    public function getAllUsers(UserService $userService) {
        // $user =

        $all_users = $this->getUserDetail(
            $userService->getAllUsers()
        );
       
        return $all_users->additional([
            'message' => 'All registered users retrieved successfully',
            'status' => "success"
        ]);
    }
}
