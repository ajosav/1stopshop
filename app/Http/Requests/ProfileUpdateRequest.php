<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class ProfileUpdateRequest extends FormRequest
{
    public $user;

    public function __construct() {
        $this->user = auth('api')->user();
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        
        return [
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|confirmed|max:255|unique:users,email,'.$this->user->id,
            'phone_number' => 'nullable|string|max:255|unique:users,phone_number,'.$this->user->id,
            'address' => 'nullable|string',
            'state' => 'nullable|string',
            'city' => 'nullable|string',
            'password' => ['nullable',
                            'string',
                            'confirmed',
                            'min:8', // must be a minimum of 8
                            'regex:/[a-z]/',
                            'regex:/[A-Z]/',
                            'regex:/[0-9]/',
                            'regex:/[@$!%*#?&]/',
            ],
            'current_password' => 'required_unless:password,=,null'
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            if(empty($this->input())) {
                $validator->errors()->add('empty parameter', 'Please pass a field to be updated');
            }
            if($this->filled('current_password')) {
                if(!Hash::check($this->input('current_password'), $this->user->password)) {
                    $validator->errors()->add('current password', 'Current password does not match');
                }
            }
        });

        return $validator;

    
    }

    public function messages() {
        return [
            "required_unless" => "The :attribute field is required"
        ];
    }

    
}
