<?php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

class RateProductRequest extends FormRequest
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
        $data = [
            'overall_rating'            =>          'required|numeric|min:1|max:5',
            'value_for_money'           =>          'nullable|numeric|min:1|max:5',
            'durability'                =>          'nullable|numeric|min:1|max:5',
            'quality'                   =>          'nullable|numeric|min:1|max:5',
            'headline'                  =>          'required|string|max:150',
            'written_review'            =>          'required|string|max:180',
            'display_name'              =>          'required|string|max:150',
            'review_photo'              =>          'nullable|array'
        ];

        if($this->filled('review_photo') && is_array($this->input('review_photo'))) {
           foreach($this->input('review_photo') as $index => $photo) {
                if(photoType($photo)) {
                    $data['review_photo'.$index] = photoType($photo) == "file" ? 'image|mimes:jpeg,jpg,png,gif,webp' : 'base64image|base64mimes:jpeg,jpg,png,gif,webp';
                }
            } 
        }
        

        return $data;
    }
}
