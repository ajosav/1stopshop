<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileImageRequest extends FormRequest
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
        return $data = [
            'profile_image' => ['required', 
                function ($attribute, $value, $fail) {
                    if ($this->getPhotoType() == false) {
                        $fail('The '.$attribute.' is invalid.');
                    }
                },
            ]
        ];

        if($this->getPhotoType()) {
            $data['profile_image'] = $this->getPhotoType() == "file" ? 'image|mimes:jpeg,jpg,png,gif,webp' : 'base64image|base64mimes:jpeg,jpg,png,gif,webp';
        }
    }

    protected function getPhotoType() {
        if ($this->filled('profile_image')) {
            return photoType($this->input('profile_image'));
        } elseif($this->file('profile_image')) {
            return photoType($this->file('profile_image'));
        }
    }
}
