<?php

namespace App\Repositories\OTP;

use App\Notifications\SendActivationCode;

class SendOTPViaSMS implements OTPInterface {

    public $user;

    public function __construct()
    {
        $this->user = auth('api')->user();
    }
    public function body() {
        $otp = $this->user->generateOTP();
        return "Your activation code is {$otp}. \n Please note that code expires in 10 minutes";
    }
    public function send() {
        $user = auth('api')->user();
        return $user->notify(new SendActivationCode);
    }
}