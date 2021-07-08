<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MailRequest extends FormRequest
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
            'subject'       => 'required',
            'recipient'     => 'required|array',
            'recipient.*'   => 'required|email',
            'cc'            => 'nullable|array',
            'cc.*'          => 'email',
            'bcc'           => 'nullable|array',
            'bcc.*'         => 'email',
            'content'       => 'required|string',
            'category'      => 'nullable',
            'pattern'       => 'nullable|in:all,individual',
            'attachment'    => 'nullable|array',
            // 'attachment.*'  => 'array',
        ];
    }
}
