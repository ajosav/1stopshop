<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait AddUUID {

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->encodedKey)) {
                $model->encodedKey = generateEncodedKey();
            }
        });
    }   

}