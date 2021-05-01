<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Helpers\ResourceHelpers;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Filters\MechanicFilter\Location;
use App\Filters\MechanicFilter\VehicleType;
use App\Filters\MechanicFilter\ProfessionalSkill;

class MechanicService {
    public function getVerifiedMechanics() {
        return User::whereHas('permissions', function($query) {
                return $query->whereName('mechanic');
            })->whereHas('mechanic');
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

    public function filterMechanicServices() {
        $filter_mechanics = app(Pipeline::class)
                        ->send(User::query())
                        ->through([
                            Location::class,
                            ProfessionalSkill::class,
                            VehicleType::class
                        ])
                        ->thenReturn();

        return $filter_mechanics;
    }
}