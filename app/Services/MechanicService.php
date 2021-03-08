<?php

namespace App\Services;

use Exception;
use App\Models\User;


class MechanicService {
    public function getVerifiedMechanics() {
        return User::whereUserType('mechanic')
                ->whereHas('userProfile', function($query) {
                    $query->where('isVerified', 1)
                        ->whereNotNull('verified_at');
                });
    }
}