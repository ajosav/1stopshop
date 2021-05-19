<?php

namespace App\Traits;

trait ExtendReview {
    public function reviewImage()
    {
        return $this->morphOne(Rating::class, 'imageable');
    }
}