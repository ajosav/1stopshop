<?php

namespace App\Http\Requests\Ad;

use App\Rules\ValidateValidAmount;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CreateAdProductRequest extends FormRequest
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

        return Gate::allows('mechanic', $this->user) ||  Gate::allows('part_dealer', $this->user);
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $data = [
            'product_title' => 'required|string|max:155',
            'product_type' => 'required|string|max:100',
            'keyword' => 'nullable|string|max:100',
            'condition' => 'required|string|max:50',
            'year' => 'required|numeric',
            'make' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'warranty' => 'required|string|max:100',
            'description' => 'required|string|max:250',
            'price' => ['required', new ValidateValidAmount],
            'negotiable' => 'nullable|in:0, 1',
            'product_photo' => 'required|array'
        ];

        foreach($this->input('product_photo') as $index => $photo) {
            if(photoType($photo)) {
                $data['product_photo'.$index] = photoType($photo) == "file" ? 'image|mimes:jpeg,jpg,png,gif,webp' : 'base64image|base64mimes:jpeg,jpg,png,gif,webp';
            }
        }

        return $data;
    }


    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $product_photo = $this->input('product_photo');
            foreach($product_photo as $photo) {
                if(photoType($photo) == false ) {
                    $validator->errors()->add('product_photo', 'Please upload a valid product photo');
                }
            }
        });

        return $validator;
    }
}
