<?php

namespace App\Observers;

use Codebyray\ReviewRateable\Models\Rating;

class RatingObserver
{
    /**
     * Handle the Rating "created" event.
     *
     * @param  \App\Models\Rating  $rating
     * @return void
     */
    public function created(Rating $rating)
    {
        $rating->notification()->create([
            'action'        => 'New review submitted',
            'author_id'     => $rating->reviewrateable_id,
            'author_type'   => $rating->reviewrateable_type,
            'owner_type'    => get_class(auth('api')->user()),
            'owner_id'      => auth('api')->user()->id
        ]);
    }

    /**
     * Handle the Rating "updated" event.
     *
     * @param  \App\Models\Rating  $rating
     * @return void
     */
    public function updated(Rating $rating)
    {
        $rating->notification()->create([
            'action' => 'Review updated',
            'author_id' => $rating->reviewrateable_id,
            'author_type' => $rating->reviewrateable_type,
            'owner_type'    => get_class(auth('api')->user()),
            'owner_id'      => auth('api')->user()->id
        ]);
    }

    /**
     * Handle the Rating "deleted" event.
     *
     * @param  \App\Models\Rating  $rating
     * @return void
     */
    public function deleted(Rating $rating)
    {
        $rating->notification()->delete();
        $rating->abuses()->delete();
        $rating->reviewExt()->delete();
        $rating->helpful()->delete();
    }

    /**
     * Handle the Rating "restored" event.
     *
     * @param  \App\Models\Rating  $rating
     * @return void
     */
    public function restored(Rating $rating)
    {
        //
    }

    /**
     * Handle the Rating "force deleted" event.
     *
     * @param  \App\Models\Rating  $rating
     * @return void
     */
    public function forceDeleted(Rating $rating)
    {
        //
    }
}
