<?php

namespace App\Traits;

trait ExtendReview {
    public function reviewext()
    {
        return $this->morphOne(Rating::class, 'imageable');
    }
}