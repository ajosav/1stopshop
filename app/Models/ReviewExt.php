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


    public function getReviewPhotoAttribute($value) {
        if(!$value) {
            return $value;
        }

        $photos = [];        

        $review_photo = json_decode($value);
        foreach($review_photo as $photo){
            try {
                $image = Storage::get($photo);
                $photos[] = Image::make($image)->encode('data-url'); 
             } catch(ImageException $e) {
                 return null;
             } catch(Exception $e) {
                 return null;
             } catch(FileNotFoundException $e) {
                 return null;
             }
        }

        return $photos;
    }
}
