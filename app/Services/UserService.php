<?php

namespace App\Services;

use Exception;
use App\Models\User;
use Illuminate\Support\Str;
use App\Helpers\AuthDataHelper;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Password;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Auth\Events\PasswordReset;
use App\DataTransferObjects\UserDataTransferObject;
use App\Helpers\ResourceHelpers;

class UserService {

    public function createUserAccount($request, $activation) {
        try {
            $create_user = DB::transaction(function() use ($request, $activation) {
                $user = User::create($request);
                $activation->sendToUser($user);
                return $user;
            });

        } catch (QueryException $e) {
            return response()->errorResponse("Error creating user data");
        } catch(Exception $e) {
            return response()->errorResponse("User registration failed", ["user_registration" => $e->getMessage()]);
        }
        return ResourceHelpers::returnAuthenticatedUser($create_user, "User Successfully Created");
    }

    public function createUserWith($data) {
        try {
            $create_user = DB::transaction(function() use ($data) {
                $user = User::firstOrCreate(['email' => $data['email']],
                    $data
                );

                return $user;
            });
        } catch (QueryException $e) {
            return response()->errorResponse("Error authenticating user");
        } catch(Exception $e) {
            return response()->errorResponse("User authentication failed", ["user_registration" => $e->getMessage()]);
        }
        return ResourceHelpers::returnAuthenticatedUser($create_user, "User authenticated successfully");
    }

    public function resetPassword($request) {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
    
                $user->setRememberToken(Str::random(60));
    
                event(new PasswordReset($user));
            }
        );
    
        return $status == Password::PASSWORD_RESET
                    ? response()->success(__($status))
                    : response()->errorResponse(__($status));
    }

    public function sendPasswordResetLink($request) {
        $status = Password::sendResetLink(
            $request->only('email')
        );
        return $status === Password::RESET_LINK_SENT
                    ? response()->success(__($status))
                    : response()->errorResponse(__($status));
    }


    public function getAllUsers() {
        return User::query();
    }
}