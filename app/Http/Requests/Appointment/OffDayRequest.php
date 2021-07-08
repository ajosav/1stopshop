<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class OffDayRequest extends FormRequest
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
            "date" => "required|date:format,Y-m-d|after_or_equal:today",
            "hour" => "required|numeric",
            'meridian' => "required|in:AM,PM",
            'isActive' => "required|in:true,false"
        ];
    }
}
