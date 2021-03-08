<?php

namespace App\Services;

use Exception;
use App\Models\User;


class PartDealerService {
    public function getVerifiedPartDealers() {
        return User::whereUserType('part_dealer')
                ->whereHas('userProfile', function($query) {
                    $query->where('isVerified', 1)
                        ->whereNotNull('verified_at');
                });
    }
}