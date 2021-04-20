<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Helpers\ResourceHelpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class MechanicService {
    public function getVerifiedMechanics() {
        return User::whereHas('permissions', function($query) {
                return $query->whereName('mechanic');
            })->whereHas('mechanics');
    }

    public function createNewMechanic($data, $user) {
        try {
            $new_mechanic = DB::transaction(function () use($data, $user) {
                if($user->mechanic()->create($data)) {
                    $user->givePermissionTo('mechanic');
                }

                return $user;
            });
        } catch (QueryException $e) {
            report($e);
            return response()->errorResponse("Error encountered while trying to create mechanic profile");
        }

        $mechanic_user = User::where('encodedKey', $new_mechanic->encodedKey)->with('mechanic', 'partDealer')->first();

        return ResourceHelpers::fullUserWithRoles($mechanic_user, 'Mechanic data created successfully');

    }
}