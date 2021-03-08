<?php

namespace App\Http\Controllers\Api\Profile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\OTP\OTPInterface;
use App\Services\Profile\CreateProfileService;
use App\Http\Requests\Auth\CreateProfileRequest;

class ProfileController extends Controller
{
    public $profileService, $activation_code;
    
    public function __construct(OTPInterface $activation_code, CreateProfileService $createProfileService)
    {
        $this->middleware('auth.jwt');
        $this->activation_code = $activation_code;
        $this->profileService = $createProfileService;
    }

    public function createProfile(CreateProfileRequest $request) {
        $user = auth('api')->user();
        return $this->profileService->createProfile($user, $request, $this->activation_code);
    }
}
