<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class AppointmentRequest extends FormRequest
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
            "mechanic_id" => "required",
            "description" => "nullable",
            "date" => "required|date:format,Y-m-d|after_or_equal:today",
            "time" => "required|numeric",
            'meridian' => 'required|in:AM,PM',
            'category' => 'nullable|string',
            'sub_category' => 'nullable|string',
            'vehicle_type' => 'required|string',
        ];
    }
}
