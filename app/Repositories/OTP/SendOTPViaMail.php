<?php

namespace App\Repositories\OTP;

use App\Notifications\SendActivationCode;

class SendOTPViaMail implements OTPInterface {

    public function body() {

    }
    public function send() {
        $user = auth('api')->user();
        return $user->notify(new SendActivationCode);
    }

    public function sendToUser($user) {
        return $user->notify(new SendActivationCode);
    }
}