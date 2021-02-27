<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Helpers\DataHelper;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Repositories\OTP\OTPInterface;
use Illuminate\Database\QueryException;
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Exception\ClientException;
use App\DataTransferObjects\UserDataTransferObject;
use App\DataTransferObjects\CompanyDataTransferObject;
use App\DataTransferObjects\ProfileDataTransferObject;
use App\Jobs\SendActivationCodeJob;

class UserService {
    
    public function createUser($request, $activation_code) {
        try {
            $data = DataHelper::userCreateData($request);
            $user = User::create($data);
            $token = JWTAuth::fromUser($user);
            if($data['user_type'] == 'regular') {
                $response = array_merge(respondWithToken($token), ['user_info' => UserDataTransferObject::create($user)]);
                return response()->success('User Successfully Created', $response);
            }

            // Create profile for user
            $profile = $this->createUserProfile($user, $request);

            // create company for user
            $company = $this->createUserCompany($user, $request);

            $activation_code->sendToUser($user);
            // SendActivationCodeJob::dispatch($user)->delay(now()->addSecond());

            $response = array_merge(respondWithToken($token), ['user_info' => UserDataTransferObject::create($user), 'profile' => ProfileDataTransferObject::create($profile), 'company' => CompanyDataTransferObject::create($company)]);
            
            return response()->success('User Successfully Created', $response);
        } catch (QueryException $e) {
            return response()->errorResponse("Error creating user data", ["errorDetails" => $e->getMessage()]);
        }
    }

    public function createUserProfile($user, $request) {
        $profile_data = DataHelper::userCreateProfile($request);
        return $user->userProfile()->create($profile_data);
    }

    public function createUserCompany($user, $request) {
        $company_data = DataHelper::userCreateCompany($request);
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

            $user_data = DataHelper::createUserWithSocialData($user_provider, $provider);
            $user = User::firstOrCreate(['email' => $user_provider->getEmail()], $user_data);
            $token = JWTAuth::fromUser($user);
            $response = array_merge(respondWithToken($token), ['user_info' => UserDataTransferObject::create($user)]);
            return response()->success('User successfully authenticated', $response);
         } catch(ClientException $e) {
            return response()->errorResponse('Link Expired', ['provider' => 'provider link has expired, please regenerate']);
         } catch(Exception $e) {
            return response()->errorResponse('Error generating user token', ['account' => $e->getMessage()]);
         }
    }
    
}