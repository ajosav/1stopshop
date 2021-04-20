<?php

namespace App\Models;

use Exception;
use App\Models\User;
use App\Traits\AddUUID;
use Intervention\Image\Facades\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;
use Intervention\Image\Exception\ImageException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartDealer extends Model
{
    use HasFactory, AddUUID, SoftDeletes;

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

    public function setCompanyPhotoAttribute($input) { 
       if($input) {
           $this->attributes['company_photo'] = !is_null($input) ? uploadImage('images/mechanic/', $input) : null;
       }
    }

    public function getCompanyPhotoAttribute($value) {
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

}
