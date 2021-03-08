<?php

namespace App\Http\Requests\Shop;

use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CreateShopRequest extends FormRequest
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
            'no_tax_id' => 'sometimes|nullable',
            'tax_identification_no' => 'required_unless:no_tax_id,1',
            'identification_type' => 'required',
            'identity_number' => 'required',
            'office_number' => 'required',
            'street_name' => 'required',
            'region' => 'required',
            'country' => 'required',
            'shop_photo' => 'required',
        ];
        
        if($this->user->user_type == 'mechanic') {
            $data =  [
                'professional_skill' => 'required',
                'specialization' => 'required',
                'experience_years' => 'required|numeric',
                'service_area' => 'required',
                'company_mission' => 'nullable'
            ];
        }

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

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            if($this->getPhotoType() == false) {
                $validator->errors()->add('shop_photo', 'Please upload a valid shop photo');
            }
        });

        return $validator;
    }


    protected function getPhotoType() {
        if ($this->filled('shop_photo')) {
            return photoType($this->input('shop_photo'));
        } elseif($this->file('shop_photo')) {
            return photoType($this->file('shop_photo'));
        }
    }

}

