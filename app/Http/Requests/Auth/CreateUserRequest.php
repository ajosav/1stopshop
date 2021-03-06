<?php

namespace App\Http\Requests\Auth;

use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function prepareForValidation() {
        $this->formatRequestInputs();
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $data =  [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'user_type' => 'nullable|in:regular,mechanic,part_dealer'
        ];

        if ($this->getSellerUserType()) {
            $data['shop_address'] = 'required';
            $data['city'] = 'required';
            $data['state'] = 'required';
            $data['profile_photo'] = 'required';
            $data['phone_number'] = 'required|unique:user_profiles';

            if($this->getPhotoType()) {
                $data['profile_photo'] = $this->getPhotoType() == "file" ? 'image|mimes:jpeg,jpg,png,gif,webp' : 'base64image|base64mimes:jpeg,jpg,png,gif,webp';
            }
        }


        return $data;
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            if($this->getSellerUserType()) {
                if($this->getPhotoType() == false) {
                    $validator->errors()->add('profile_photo', 'Please upload a valid profile photo');
                }
            }
        });

        return $validator;
    }

    protected function formatRequestInputs()
    {
        if ($this->filled('phone_number')) {
            $this->merge(['phone_number' => sanitizePhoneNumber($this->input('phone_number'), false)]);
        }
    }

    protected function getPhotoType() {
        if ($this->filled('profile_photo')) {
            return photoType($this->input('profile_photo'));
        } elseif($this->file('profile_photo')) {
            return photoType($this->file('profile_photo'));
        }
    }

    protected function getSellerUserType() {
        return $this->filled('user_type') && ($this->input('user_type') == 'mechanic' || $this->input('user_type') == 'part_dealer');
    }
}
