<?php

namespace App\Models;

use Codebyray\ReviewRateable\Models\Rating;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoundHelpful extends Model
{
    use HasFactory;

    protected  $guarded = [];

    public function rating() {
        return $this->belongsTo(Rating::class);
    }
}
