<?php

namespace App\Http\Resources\WorkHours;

use Illuminate\Http\Resources\Json\JsonResource;

class OffDaysResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'date' => $this->date,
            'hour' => $this->hour,
            'meridian' => $this->meridian,
            'isActive' => $this->isActive,
        ];
    }
}
