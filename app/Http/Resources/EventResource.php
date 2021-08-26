<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            "title"     => $this->title,
            "start"     => $this->start,
            "end"       => $this->end,
            "time"      => $this->time,
            "all_day"   => $this->all_day
        ];
    }
}
