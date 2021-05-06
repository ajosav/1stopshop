<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
}
