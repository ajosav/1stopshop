<?php

namespace App\Http\Resources\Abuse;

use Illuminate\Support\Str;
use Illuminate\Http\Resources\Json\JsonResource;

class AbusesReource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if($this->abusable_type == "App\Models\AdService") {
            $abuse_type = 'Product';
            // $reported_by = $this->abusable;
        } else {
            $abuse_type = 'Review';
            // $reported_by = $this->abusable;
        }

        
        return [
            'full_name' => $this->full_name,
            'email' => $this->email,
            'message' => $this->message,
            'created_date' => $this->created_at->diffForHumans(),
            'abuse_type' => $abuse_type,
            'report_by' => $this->abusable
        ];
    }
}
