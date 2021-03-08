<?php

namespace App\Http\Requests\Auth;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class CreateProfileRequest extends FormRequest
{
    public $user;

    public function __construct()
    {
        $this->user = auth('api')->user();
    }
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('isMechanic', $this->user->encodedKey) ||  Gate::allows('isPartDealer', $this->user->encodedKey);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $data =  [
            'user_type' => 'sometimes|required|in:regular,mechanic,part_dealer',
            'phone_number' => 'required|unique:user_profiles',
            'no_tax_id' => 'sometimes|nullable|in:0, 1',
            'tax_identification_no' => 'required_unless:no_tax_id,1',
            'identification_type' => 'required',
            'identity_number' => 'required',
            'office_number' => 'required',
            'street_name' => 'required',
            'city' => 'required|string|max:100',
            'state' => 'required|string',
            'professional_skill' => 'required_if:user_type,mechanic',
            'specialization' => 'required_if:user_type,mechanic',
            'experience_years' => 'required_if:user_type,mechanic|numeric',
            'service_area' => 'required_if:user_type,mechanic',
            'shop_photo' => ['required', function ($attribute, $value, $fail) {
                if ($this->getPhotoType() == false) {
                    $fail('The '.$attribute.' is invalid.');
                }
            },],
        ];

        if($this->getPhotoType()) {
            $data['shop_photo'] = $this->getPhotoType() == "file" ? 'image|mimes:jpeg,jpg,png,gif,webp' : 'base64image|base64mimes:jpeg,jpg,png,gif,webp';
        }


        return $data;
    }

    public function messages()
    {
        return [
            'required_if'     => 'The :attribute field is required.',
            'required_unless' => 'The :attribute field is required.',
        ];
    }


    protected function formatRequestInputs()
    {
        if ($this->filled('phone_number')) {
            $this->merge(['phone_number' => sanitizePhoneNumber($this->input('phone_number'), false)]);
        }
    }

    protected function getPhotoType() {
        if ($this->filled('shop_photo')) {
            return photoType($this->input('shop_photo'));
        } elseif($this->file('shop_photo')) {
            return photoType($this->file('shop_photo'));
        }
    }

    
}
