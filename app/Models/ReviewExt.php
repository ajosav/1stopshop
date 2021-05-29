<?php

namespace App\Models;

use Exception;
use Intervention\Image\Facades\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Exception\ImageException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class ReviewExt extends Model
{
    use HasFactory;

    protected $guarded = [];

    // public function ratings()
    // {
    //     return $this->morphMany(Rating::class, 'author');
    // }

    public function imageable()
    {
        return $this->morphTo();
    }

}
