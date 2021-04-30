<?php

namespace App\Models;

use App\Traits\AddUUID;
use Exception;
use Intervention\Image\Facades\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Intervention\Image\Exception\ImageException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class Mechanic extends Model
{
    use HasFactory, SoftDeletes, AddUUID;

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
    public function setProfessionalSkillAttribute($input) { 
       if($input) {
           $this->attributes['professional_skill'] = json_encode($input);
       }
    }
    public function setVehicleTypeAttribute($input) { 
       if($input) {
           $this->attributes['vehicle_type'] = json_encode($input);
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

    public function getProfessionalSkillAttribute($value) {
        if(!$value) {
            return $value;
        }
        return json_decode($value, true);
    }
    public function getVehicleType($value) {
        if(!$value) {
            return $value;
        }
        return json_decode($value, true);
    }
}
