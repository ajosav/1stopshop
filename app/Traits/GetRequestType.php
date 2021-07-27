<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\User\UserResourceCollection;
use App\Http\Resources\Product\ProductResourceCollection;
use App\Http\Resources\Product\RelatedProductResorceCollection;


trait GetRequestType {
    public function getUserDetail($user) {
        if(request()->has('fullDetails') && request('fullDetails') === 'true') {
            $retrieved_user = $user->with('mechanic', 'partDealer', 'permissions')->paginate(20);
            return UserResourceCollection::collection($retrieved_user);
        }
        
        return UserResource::collection($user->paginate(20));
    }
    
    public function getSingleUser($user) {
        if(request()->has('fullDetails') && request('fullDetails') === 'true') {
            $new_user = $user->with(['mechanic', 'partDealer', 'permissions'])->firstOrFail();
            return new UserResourceCollection($new_user);
        }
        
        return new UserResource($user->firstOrFail());
    }

    public function getFullProductDetails($product) {
        if(request()->has('fullDetails') && request('fullDetails') === 'true') {
            $retrieved_product = $product->with('user', 'category', 'userViewContact', 'productViews', 'ratings', 'notifications')->paginate(20);
            return ProductResourceCollection::collection($retrieved_product);
        }
        
        return ProductResource::collection($product->with('user', 'productViews', 'userViewContact', 'ratings', 'notifications')->paginate(20));
    }

    public function getSingleProduct($product) {
        if(request()->has('fullDetails') && request('fullDetails') === 'true') {
            $retrieved_product = $product->with('user', 'category', 'userViewContact', 'productViews', 'notifications')->firstOrFail();
            return new ProductResourceCollection($retrieved_product);
        }
        
        return new ProductResource($product->with('productViews', 'userViewContact', 'notifications')->firstOrFail());
    }
    public function getSingleRelatedProduct($product) {
        if(request()->has('fullDetails') && request('fullDetails') === 'true') {
            return new RelatedProductResorceCollection($product->with('user', 'category', 'userViewContact', 'productViews', 'notifications')->firstOrFail());
        }
        
        return new ProductResource($product->with('user', 'category', 'userViewContact', 'productViews', 'notifications')->firstOrFail());
    }
    
}