<?php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

class RateMechanicRequest extends FormRequest
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
        return [
            'overall_rating'            =>          'required|numeric|min:1|max:5',
            'response_to_time'          =>          'nullable|numeric|min:1|max:5',
            'professionalism'           =>          'nullable|numeric|min:1|max:5',
            'experience'                =>          'nullable|numeric|min:1|max:5',
            'headline'                  =>          'required|string|max:150',
            'written_review'            =>          'required|string|max:180'
        ];
    }
}
