<?php

namespace App\Services\AdProductService;

use Exception;
use App\Models\AdService;
use Illuminate\Support\Facades\DB;
use App\Helpers\AdProductDataHelper;
use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\Product\ProductResourceCollection;
use Illuminate\Database\QueryException;

class AdProductActionService {

    public function createProduct($user, $request) {
        try {
            $create_ad = DB::transaction(function() use ($user, $request) {

                $data = AdProductDataHelper::createNewProductData($request);
                return  $user->adService()->create($data);
            });

            $new_Ad = new ProductResource($create_ad);

            return response()->success('New Product Successfully Created', $new_Ad);
        } catch (QueryException $e) {
            return response()->errorResponse("Error creating product");
        } catch(Exception $e) {
            return response()->errorResponse("Product creation failed", ["Product Creation" => $e->getMessage()]);
        }
    }

    public function viewAllAds() {
        return AdService::query();
    }

    public function findProductByUser($userEncodedKey) {
        return $this->viewAllAds()->with('adProductType')->whereHas('user', function($query) use ($userEncodedKey) {
            $query->where('encodedKey', $userEncodedKey);
        })->with('user');
    }

    public function findProductByEncodedKey($productEncodedKey) {
        return $this->viewAllAds()->with('adProductType')->where('encodedKey', $productEncodedKey);
    }


}