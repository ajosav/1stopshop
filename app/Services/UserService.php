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
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Auth\Events\PasswordReset;
use App\DataTransferObjects\UserDataTransferObject;

class UserService {
    
    public function createUser($request, $activation_code) {
        try {
            $create_user = DB::transaction(function() use ($request, $activation_code) {
                $data = AuthDataHelper::userCreateData($request);
                $user = User::create($data);
                $token = JWTAuth::fromUser($user);
                if($data['user_type'] == 'regular') {
                    $response = array_merge(respondWithToken($token), ['user_info' => UserDataTransferObject::create($user)]);
                    return response()->success('User Successfully Created', $response);
                }

                // Create profile for user
                if(!$profile = $this->createUserProfile($user, $request)) {
                    throw new Exception("Error encountered while creating user profile");
                }

                // create company for user
                if(!$company = $this->createUserCompany($user, $request)) {
                    throw new Exception("Error encountered creating company profile");
                }

                $activation_code->sendToUser($user);
                // SendActivationCodeJob::dispatch($user)->delay(now()->addSecond());

                $response = array_merge(respondWithToken($token), $user->getFullUserDetail());
                
                return $response;
            });

            return response()->success('User Successfully Created', $create_user);
        } catch (QueryException $e) {
            return response()->errorResponse("Error creating user data");
        } catch(Exception $e) {
            return response()->errorResponse("User registration failed", ["user_registration" => $e->getMessage()]);
        }
    }

    public function createUserProfile($user, $request) {
        $profile_data = AuthDataHelper::userCreateProfile($request);
        return $user->userProfile()->create($profile_data);
    }

    public function createUserCompany($user, $request) {
        $company_data = AuthDataHelper::userCreateCompany($request);
        return $user->company()->create($company_data);
    }

    public function generateSocialLink($provider) {
        $providers = config('socialauth.providers');
        if(!in_array($provider, $providers)) {
            return response()->errorResponse("Social provider not supported");
        }
        try {
            $redirect_url = (string) Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();
            return response()->success("Authentication link successfully generated", ['href' => $redirect_url, 'provider' => $provider]);
        } catch(Exception $e) {
            return response()->errorResponse("Error generating authentication link", ['provider' => 'Could not generate redirect link for provider']);
        }
    }

    public function loginViaSocial($provider) {
        try {
            $user_provider = Socialite::driver($provider)->stateless()->user();
            if($user_provider->getEmail() == '') {
                return response()->errorResponse("Could not retrieve user email", ["provider" => "{$provider} didn't send user's email, please ensure email is enable"]);
            }

            $user_data = AuthDataHelper::createUserWithSocialData($user_provider, $provider);
            $user = User::firstOrCreate(['email' => $user_provider->getEmail()], $user_data);
            $token = JWTAuth::fromUser($user);
            $response = array_merge(respondWithToken($token), ['user_info' => UserDataTransferObject::create($user)]);
            return response()->success('User successfully authenticated', $response);
         } catch(ClientException $e) {
            return response()->errorResponse('Authentication Failed', ['provider' => $e->getMessage()]);
         } catch(Exception $e) {
            return response()->errorResponse('Error generating user token', ['account' => $e->getMessage()]);
         }
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
    
}