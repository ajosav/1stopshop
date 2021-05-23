<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Helpers\ResourceHelpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;


class PartDealerService {
    public function getVerifiedPartDealers() {
        return User::whereHas('permissions', function($query) {
                return $query->whereName('part_dealer');
            })->whereHas('partDealer');
    }

    public function createNewPartDealer($data, $user) {
        try {
            $new_dealer = DB::transaction(function () use($data, $user) {
                if($user->partDealer()->create($data)) {
                    $user->givePermissionTo('part_dealer');
                }

                return $user;
            });
        } catch (QueryException $e) {
            report($e);
            return response()->errorResponse("Error encountered while trying to create part dealer");
        }

        $part_dealer = User::where('encodedKey', $new_dealer->encodedKey)->with('partDealer', 'mechanic')->first();

        return ResourceHelpers::fullUserWithRoles($part_dealer, 'Mechanic data created successfully');
    }

    public function updatePartDealer($data, $user) {
        try {
            $part_dealer = $user->partDealer;

            foreach($data as $index => $update) {
                if(!is_null($update) && $update !== "") {
                    if($index == 'company_photo') {
                        if(file_exists(storage_path("app/" . $part_dealer->company_photo))) {
                            @unlink(storage_path("app/" . $part_dealer->company_photo));
                        }
                    }
                    $part_dealer->$index = $update;
                }
            }

            if(!$part_dealer->isDirty()) {
                return response()->success("Nothing Changed");
            }

            if(!$part_dealer->save()) {
                return response()->errorResponse('Error updating Part Dealer profile');
            }
        } catch (QueryException $e) {
            report($e);
            return response()->errorResponse("Error encountered while trying to update part dealer");
        }

        $updated_part_dealer = User::where('encodedKey', $user->encodedKey)->with('partDealer', 'mechanic')->first();

        return ResourceHelpers::fullUserWithRoles($updated_part_dealer, 'Part Dealer profile data updated successfully');
    }
}