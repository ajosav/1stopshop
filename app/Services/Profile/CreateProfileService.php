<?php

namespace App\Services\Profile;

use Exception;
use App\Models\User;
use App\Helpers\AuthDataHelper;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Jobs\SendActivationCodeJob;
use Illuminate\Database\QueryException;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Resources\User\UserResourceCollection;

class CreateProfileService {
    public function createProfile($user, $request, $activation_code) {
        try {
            $create_profile = DB::transaction(function() use ($user, $request, $activation_code) {
                // Create profile for user
                if(!$this->createUserProfile($user, $request)) {
                    throw new Exception("Error encountered while creating user profile");
                }

                // create company for user
                if(!$this->createUserCompany($user, $request)) {
                    throw new Exception("Error encountered creating company profile");
                }

                $activation_code->sendToUser($user);
                // SendActivationCodeJob::dispatch($user)->delay(now()->addSecond());

                return $user;
            });
            $update_user = User::where('encodedKey', $create_profile->encodedKey)->get();
            $full_user_detail = UserResourceCollection::collection($update_user);

            return response()->success('User Profile Successfully Created', $full_user_detail);
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
            $redirect_url = (string) Socialite::driver($provider)
                            ->stateless()
                            ->with(['state' => 'mechanic'])
                            ->redirect()
                            ->getTargetUrl();
            return response()->success("Authentication link successfully generated", ['href' => $redirect_url, 'provider' => $provider]);
        } catch(Exception $e) {
            return response()->errorResponse("Error generating authentication link", ['provider' => 'Could not generate redirect link for provider']);
        }
    }
}