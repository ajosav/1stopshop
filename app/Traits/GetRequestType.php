<?php

namespace App\Traits;

use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\Product\ProductResourceCollection;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\User\UserResourceCollection;


trait GetRequestType {
    public function getUserDetail($user) {
        if(request()->has('fullDetails') && request('fullDetails') === 'true') {
            $retrieved_user = $user->with('company', 'userProfile')->jsonPaginate();
            return UserResourceCollection::collection($retrieved_user);
        }
        
        return UserResource::collection($user->jsonPaginate());
    }
    
    public function getSingleUser($user) {
        if(request()->has('fullDetails') && request('fullDetails') === 'true') {
            $new_user = $user->with('mechanic', 'partDealer')->firstOrFail();
            return new UserResourceCollection($new_user);
        }
        
        return new UserResource($user->firstOrFail());
    }

    public function getFullProductDetails($product) {
        if(request()->has('fullDetails') && request('fullDetails') === 'true') {
            $retrieved_product = $product->with('user')->jsonPaginate();
            return ProductResourceCollection::collection($retrieved_product);
        }
        
        return ProductResource::collection($product->jsonPaginate());
    }

    public function getSingleProduct($product) {
        if(request()->has('fullDetails') && request('fullDetails') === 'true') {
            $retrieved_product = $product->with('user')->firstOrFail();
            return new ProductResourceCollection($retrieved_product);
        }
        
        return new ProductResource($product->firstOrFail());
    }
}