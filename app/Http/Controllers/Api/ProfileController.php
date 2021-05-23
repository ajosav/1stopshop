<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Helpers\ResourceHelpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileImageRequest;
use App\Http\Requests\ProfileUpdateRequest;

class ProfileController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware('auth.jwt');
        $this->user = auth('api')->user();
    }

    public function profileUpdate(ProfileUpdateRequest $request) {
        $new_update = $request->validated();

        foreach($new_update as $index => $update) {
            if(!is_null($update) && $update !== "" && $index !== "current_password") {
                $this->user->$index = $update;
            }
        }

        if(!$this->user->isDirty()) {
            return response()->success("Nothing Changed");
        }

        if(!$this->user->save()) {

            return response()->errorResponse('Error updating user profile');
        }

        return response()->success('User profile successfully updated');
    }

    public function uploadProfile(ProfileImageRequest $request) {
        $photo = $request->validated();

        if(file_exists(storage_path("app/" . $this->user->profile_image))) {
            @unlink(storage_path("app/" . $this->user->profile_image));
        }

        $this->user->profile_image = uploadImage('images/profile/', $photo['profile_image']);
        
        if(!$this->user->save()){
            return response()->errorResponse('Unable to upload profile image');
        }

        return ResourceHelpers::returnUserData($this->user, "Profile image uploaded successfully");
    }
}
