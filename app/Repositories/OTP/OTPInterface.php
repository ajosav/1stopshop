<?php

namespace App\Repositories\OTP;

interface OTPInterface {
    public function body();
    public function send();
}