<?php

namespace App\Http\Resources\WorkHours;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class WorkHoursResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $hours = json_decode($this->schedule);
        return [
            'day' => $this->day,
            'hours_available' => Arr::sort($hours)
        ];
    }
}
