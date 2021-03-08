<?php

namespace App\Services\Shop;

use Exception;
use App\Models\User;
use App\Helpers\ShopDataHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;


class GeneralShopService {
    public function createShop($request, $user) {
        try {
            $updated_user = DB::transaction(function() use ($request, $user) {
                // Create profile for user
                if(!$profile = $this->updateUserProfile($user, $request)) {
                    throw new Exception("Error encountered while updating user profile");
                }

                // create company for user
                if(!$company = $this->updateUserCompany($user, $request)) {
                    throw new Exception("Error encountered updating company profile");
                }

                return $user;
            });

            $updated_user = User::with('userProfile', 'company')->find($user->id);

            return response()->success('Shop Successfully Registered', $updated_user->getFullUserDetail());
        } catch (QueryException $e) {
            return response()->errorResponse("Error registering shop");
        } catch(Exception $e) {
            return response()->errorResponse("Shop registration failed", ["shop_registration" => $e->getMessage()]);
        }
    }


    public function updateUserProfile($user, $request) {
        $profile_data = collect(ShopDataHelper::userUpdateProfile($request))->filter(function ($value) { return !is_null($value); });
        return $user->userProfile()->update($profile_data->all());
    }

    public function updateUserCompany($user, $request) {
        $company_data = collect(ShopDataHelper::userUpdateCompany($request))->filter(function ($value) { return !is_null($value); });
        return $user->company()->update($company_data->all());
    }
}