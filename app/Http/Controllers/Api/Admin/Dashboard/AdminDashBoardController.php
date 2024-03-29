<?php

namespace App\Http\Controllers\Api\Admin\Dashboard;

use App\Models\User;
use App\Models\Mechanic;
use App\Models\AdService;
use App\Models\ProductView;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Traits\GetRequestType;
use Illuminate\Pipeline\Pipeline;
use App\Filters\UserFilter\Search;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\User\UserResourceCollection;
use Codebyray\ReviewRateable\Models\Rating;
use App\Http\Resources\Review\UserReviewResource;
use App\Http\Resources\Review\ProductReviewResource;
use App\Models\Abuse;
use App\Models\Appointment;
use App\Models\Message;

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

    public function fetchUsersWithDate() {
        request()->validate([
            'date' => 'required|date:format,Y-m-d|before_or_equal:today'
        ]);
        $creation_date = request()->query('date');
        $mechanics      = User::join('mechanics', 'users.id', '=', 'mechanics.user_id')->whereDate('users.created_at', $creation_date)->get();
        $part_dealers   = User::join('part_dealers', 'users.id', '=', 'part_dealers.user_id')->whereDate('users.created_at', $creation_date)->get();
        $users_count    = User::whereDate('created_at', $creation_date)->get();
        $regular_user   = User::doesntHave('mechanic')->doesntHave('partDealer')->whereDate('created_at', $creation_date)->get();

        return response()->success("Successfully returned users count", [
            'mechanics' => $mechanics,
            'part_dealers' => $part_dealers,
            'regular_user' => $regular_user,
            'total_users' => $users_count
        ]);
    }

    public function getAllUsers(UserService $userService) {
        $filter_users = app(Pipeline::class)
                        ->send($userService->getAllUsers())
                        ->through([
                            Search::class
                        ])
                        ->thenReturn();
        $all_users = $this->getUserDetail(
            $filter_users
        );
       
        return $all_users->additional([
            'message' => 'All registered users retrieved successfully',
            'status' => "success"
        ]);
    }

    public function getUsersByDate(UserService $userService, $date) {
        $filter_users = $userService->getAllUsers()->whereDate('created_at', $date);
        $all_users = $this->getUserDetail(
            $filter_users
        );
       
        return $all_users->additional([
            "message" => "All registered users on {$date} retrieved successfully",
            "status" => "success"
        ]);
    }

    public function admins() {
        $admin_users = User::whereHas('permissions', function($query) {
            return $query->whereIn('name', [
                'admin_user',
                'super_admin',
                'admin_user_2'
            ]);
        });

        $filter_users = app(Pipeline::class)
                    ->send($admin_users)
                    ->through([
                        Search::class
                    ])
                    ->thenReturn();

        return $this->getUserDetail(
                    $filter_users
                )->additional([
                    'message' => 'All registered users retrieved successfully',
                    'status' => "success"
                ]);
    }

    public function salesAnalytics() {
        $mechanics_count = Mechanic::count();
        $products_count = AdService::count();
        $product_views = ProductView::groupBy('ad_id')->count();

        return response()->success('Sales analytics returned successfully', [
            'totalMechanics'    =>  $mechanics_count,
            'totalProducts'     =>  $products_count,
            'productsViewed'    =>  $product_views
        ]);
    }

    public function getPermissions() {
        $permissions = DB::table('permissions')->select('name')->where('guard_name', 'api')->get();

        return response()->success('All available permissions retrieved successfully', $permissions->map(function($data) {
            return $data->name;
        }));
    }

    public function productAbuses(AdService $adservice) {
        return response()->success('Product reported abuses return successfully', $adservice->abuses->map->format());
    }
    public function allProductAbuses() {
        $abuses = Abuse::where('abusable_type', 'App\Models\AdService')->with('abusable')->orderBy('created_at', 'desc')->get()->map->format();

        return response()->success('All reported abuses retrieved successfully', $abuses);
    }

    public function allProductReviews() {
        // from the request
        $reviews = Rating::where('reviewrateable_type', 'App\Models\AdService')->with(['reviewrateable', 'helpful'])->latest()->paginate(20);
        return (ProductReviewResource::collection($reviews))->additional([
            'message' => 'Products reviews retrieved successfully',
            'status' => 'success'
        ]);
    }

    public function allMechanicReviews() {
        $reviews = Rating::where('reviewrateable_type', 'App\Models\Mechanic')->with('reviewrateable')->latest()->paginate(20);
        return (UserReviewResource::collection($reviews))->additional([
            'message' => 'Services reviews retrieved successfully',
            'status' => 'success'
        ]);
    }

    public function deleteProduct(AdService $adService) {
        abort_if(!request()->user()->can('super_admin'), 403, "Permission Denied");
        
        if(!$adService->delete()) {
            return response()->errorResponse("Error Deleting Product");
        }

        return response()->success('Product successfully deleted');
    }

    public function completedAppointments() {
        $completed = Appointment::whereDate('date', '<', now())->get();

        return response()->success("Completed appointments retrieved sucessfully", $completed);
    }

    public function pendingAppointments() {
        $pending_appointment = Appointment::whereDate('date', '>', now())->orWhere('date', now())->get();

        return response()->success("Pending appointments retrieved sucessfully", $pending_appointment);
    }

    public function cancelledAppointment() {
        $pending_appointment = Appointment::onlyTrashed()->get();

        return response()->success("Deleted appointments retrieved sucessfully", $pending_appointment);

    }

    public function getAllMessages() {
        $messages = Message::latest()->get();
        return (MessageResource::collection($messages))->additional([
            'status' => 'success',
            'message' => 'Messages retrieved successfully'
        ]);
        // return response()->success('Get all messages');
    }
}
