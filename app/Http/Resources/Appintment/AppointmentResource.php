<?php

namespace App\Http\Resources\Appintment;

use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $appointment_time = $this->hour;
        if($this->meridian == "PM" && $this->hour != 00) {
            $appointment_time = (int) $this->hour - 12;
        }

        if($this->meridian == "AM" && $this->hour == 00) {
            $appointment_time = 12;
        }
        return [
            'id' => encrypt($this->id),
            "visitor_first_name" => $this->visitor->first_name,
            "visitor_last_name" => $this->visitor->last_name,
            "visitor_id" => $this->visitor_id,
            "description" => $this->description,
            "date" => $this->date,
            "time" => $appointment_time,
            "meridian" => $this->meridian,
            "status" => $this->status
        ];
    }
}
