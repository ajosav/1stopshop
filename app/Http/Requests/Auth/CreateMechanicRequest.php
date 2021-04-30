<?php

namespace App\Http\Requests\Auth;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class CreateMechanicRequest extends FormRequest
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
        return !$this->user->hasPermissionTo('mechanic');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $data =  [
            'phone_number' => 'required|unique:mechanics',
            'tax_identification_no' => 'nullable',
            'identification_type' => 'required',
            'identity_number' => 'required',
            'professional_skill' => 'required',
            // 'specialization' => 'required',
            'experience_years' => 'required|numeric',
            // 'service_area' => 'required',
            'office_address' => 'required',
            'state' => 'required|string',
            'city' => 'required|string|max:100',
            'working_hours' => 'required|string',
            'company_photo' => ['required', function ($attribute, $value, $fail) {
                if ($this->getPhotoType() == false) {
                    $fail('The '.$attribute.' is invalid.');
                }
            },],
        ];

        if($this->getPhotoType()) {
            $data['company_photo'] = $this->getPhotoType() == "file" ? 'image|mimes:jpeg,jpg,png,gif,webp' : 'base64image|base64mimes:jpeg,jpg,png,gif,webp';
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
        if ($this->filled('company_photo')) {
            return photoType($this->input('company_photo'));
        } elseif($this->file('company_photo')) {
            return photoType($this->file('company_photo'));
        }
    }

    
}
