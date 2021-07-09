<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abuse extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the parent abusable model (product or review).
     */
    public function abusable()
    {
        return $this->morphTo();
    }
}
