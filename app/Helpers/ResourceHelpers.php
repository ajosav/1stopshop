<?php

namespace App\Helpers;

use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\User\AuthUserResource;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\User\UserResourceCollection;

class ResourceHelpers {
    public static function returnUserData($user, $message = 'User data successfully retrieved') : UserResource {
        return (new UserResource($user))->additional([
            'message' => $message,
            'status' => "success"
        ]);
    }


    public static function returnAuthenticatedUser($user, $message) : AuthUserResource {
        return (new AuthUserResource($user))->additional([
            'message' => $message,
            'status' => "success"
        ]);
    }

    public static function fullUserWithRoles($user, $message) : UserResourceCollection {
        return (new UserResourceCollection($user))->additional([
            'message' => $message,
            'status' => "success"
        ]); 
    }

    public static function categoriesWithSubs($category, $message) {
        return (CategoryResource::collection($category))->additional([
            'message' => $message,
            'status' => "success"
        ]);
    }
}