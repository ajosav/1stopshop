<?php

namespace App\Http\Controllers\Api\Admin\Auth;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use App\Http\Requests\Auth\LoginRequest;
use App\Notifications\AdminUserCreatedNotification;
use App\Http\Requests\Admin\Auth\CreateNewUserRequest;
use App\Http\Requests\Admin\PermissionRequest;

class AdminAuthController extends Controller
{
    public function login(LoginRequest $request) {
        if(auth('api')->check()) {
            auth('api')->logout();
        };
        return $request->login();
    }

    public function createUser(CreateNewUserRequest $request) {
        $new_user = $request->validated();
        try {
            $create_user = DB::transaction(function() use ($new_user, $request) {
                $user = User::create($new_user);
                if(!$user->hasPermissionTo('admin_user')) {
                    $user->givePermissionto('admin_user');
                }
                $user->notify(new AdminUserCreatedNotification($request));
                return $user;
            });

            return response()->success('Admin account created successfully');

        } catch (QueryException $e) {
            return response()->errorResponse("Error creating admin user");
        } catch(Exception $e) {
            return response()->errorResponse("Admin registration failed", ["user_registration" => $e->getMessage()]);
        }
    }

    public function grantPermission(PermissionRequest $request, User $user) {
        $permission = $request->permission;
        if($user->hasPermissionTo($permission)) {
            return response()->success("User already has permission '{$permission}'", [], 203);
        }

        if(!$user->givePermissionTo($permission)) {
            return response()->errorResponse("Error occured while trying to give permission '{$permission}' user");
        }

        return response()->success("Permission '{$permission}' granted to user");
    }

    public function revokePermission(PermissionRequest $request, User $user) {
        $permission = $request->permission;
        if(!$user->hasPermissionTo($permission)) {
            return response()->success("User does not have '{$permission}' permission", [], 203);
        }

        if(!$user->revokePermissionTo($permission)) {
            return response()->errorResponse("Error occured while trying to revoke permission '{$permission}' from user");
        }

        return response()->success("Permission '{$permission}' revoked from user");
    }
}
