<?php

namespace App\Models;

use Exception;
use Intervention\Image\Facades\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Intervention\Image\Exception\ImageException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class UserProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $dates = [
        'updated_at',
        'created_at',
        'deleted_at',
        'verified_at'
    ];

    public function getRouteKeyName()
    {
        return 'encodedKey';
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function getProfilePhotoAttribute($value) {
        if(!$value) {
            return $value;
        }
        try {
           $image = Storage::get($value);
            return (string) Image::make($image)->encode('data-url'); 
        } catch(ImageException $e) {
            return null;
        } catch(Exception $e) {
            return null;
        } catch(FileNotFoundException $e) {
            return null;
        }
        
    }

    public function getServiceAreaAttribute($value) {
        if(!$value) {
            return $value;
        }
        return json_decode($value, true);
    }
}
