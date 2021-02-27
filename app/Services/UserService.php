<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Helpers\DataHelper;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Jobs\SendActivationCodeJob;
use Illuminate\Support\Facades\Hash;
use App\Repositories\OTP\OTPInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Password;
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Auth\Events\PasswordReset;
use App\DataTransferObjects\UserDataTransferObject;
use App\DataTransferObjects\CompanyDataTransferObject;
use App\DataTransferObjects\ProfileDataTransferObject;

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

            $response = array_merge(respondWithToken($token), $user->getFullUserDetail());
            
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