<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class ProfileUpdateRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user = auth('api')->user();
        return [
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|confirmed|max:255|unique:users,email,'.$user->id,
            'password' => ['nullable',
                            'string',
                            'confirmed',
                            'min:8', // must be a minimum of 8
                            'regex:/[a-z]/',
                            'regex:/[A-Z]/',
                            'regex:/[0-9]/',
                            'regex:/[@$!%*#?&]/',
            ]
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            if(empty($this->input())) {
                $validator->errors()->add('empty parameter', 'Please pass a field to be updated');
            }
        });

        return $validator;
    }

    
}
