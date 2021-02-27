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
            'user_type' => 'sometimes|required'
        ];

        if ($this->getSellerUserType()) {
            $data['shop_address'] = 'required';
            $data['city'] = 'required';
            $data['state'] = 'required';
            $data['shop_photo'] = 'required';
            $data['phone_number'] = 'required|unique:user_profiles';

            if($this->getPhotoType()) {
                $data['shop_photo'] = $this->getPhotoType() == "file" ? 'image|mimes:jpeg,jpg,png,gif,webp' : 'base64image|base64mimes:jpeg,jpg,png,gif,webp';
            }
        }


        return $data;
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            if($this->getSellerUserType()) {
                if($this->getPhotoType() == false) {
                    $validator->errors()->add('shop_photo', 'Please upload a valid shop photo');
                }
            }
        });

        return $validator;
    }

    protected function formatRequestInputs()
    {
        if ($this->filled('phone_number')) {
            $this->merge(['dest_user_info' => sanitizePhoneNumber($this->input('phone_number'), false)]);
        }
    }

    protected function getPhotoType() {
        if ($this->filled('shop_photo')) {
            return photoType($this->input('shop_photo'));
        } elseif($this->file('shop_photo')) {
            return photoType($this->file('shop_photo'));
        }
    }

    protected function getSellerUserType() {
        return $this->filled('user_type') && ($this->input('user_type') == 'mechanic' || $this->input('user_type') == 'part_dealer');
    }
}
