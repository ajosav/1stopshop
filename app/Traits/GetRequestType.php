<?php

namespace App\Traits;

use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\Product\ProductResourceCollection;
use App\Http\Resources\Product\RelatedProductResorceCollection;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\User\UserResourceCollection;


trait GetRequestType {
    public function getUserDetail($user) {
        if(request()->has('fullDetails') && request('fullDetails') === 'true') {
            $retrieved_user = $user->with('mechanic', 'partDealer')->paginate();
            return UserResourceCollection::collection($retrieved_user);
        }
        
        return UserResource::collection($user->paginate(50));
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
            $retrieved_product = $product->with('user', 'category', 'userViewContact', 'productViews')->paginate(50);
            return ProductResourceCollection::collection($retrieved_product);
        }
        
        return ProductResource::collection($product->with('productViews', 'userViewContact')->paginate(50));
    }

    public function getSingleProduct($product) {
        if(request()->has('fullDetails') && request('fullDetails') === 'true') {
            $retrieved_product = $product->with('user', 'category', 'userViewContact', 'productViews')->firstOrFail();
            return new ProductResourceCollection($retrieved_product);
        }
        
        return new ProductResource($product->with('productViews', 'userViewContact')->firstOrFail());
    }
    public function getSingleRelatedProduct($product) {
        if(request()->has('fullDetails') && request('fullDetails') === 'true') {
            $retrieved_product = $product->with(['user', 'category', 'productViews'])->firstOrFail();
            return new RelatedProductResorceCollection($retrieved_product);
        }
        
        return new ProductResource($product->with('productViews')->firstOrFail());
    }
    
}