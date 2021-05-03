<?php

namespace App\Models;

use Exception;
use App\Traits\AddUUID;
use Intervention\Image\Facades\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Intervention\Image\Exception\ImageException;
use Codebyray\ReviewRateable\Contracts\ReviewRateable;
use Codebyray\ReviewRateable\Traits\ReviewRateable as ReviewRateableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class Mechanic extends Model implements ReviewRateable
{
    use HasFactory, SoftDeletes, AddUUID, ReviewRateableTrait;

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
    public function setWorkingHoursAttribute($input) { 
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

    public function getWorkingHoursAttribute($value) {
        if(!$value) {
            return $value;
        }
        return json_decode($value, true);
    }

    public function starRatingPercent($max = 5)
    {
        $ratings = $this->ratings();
        $quantity = $ratings->count();
        $total = $ratings->selectRaw("SUM(rating) as total")->where('rating', $max)->pluck('total')->first();
        // return $total;
        return ($quantity * $max) > 0 ? $total / (($quantity * $max) / 100) : 0;
    }

    public function customerReviews() {
        return [
            "average_overall_rating" => $this->averageRating(2),
            "average_professionalism" => $this->averageCustomerServiceRating(2),
            "average_experience" =>  $this->averageQualityRating(2),
            "average_response_to_time" => $this->averageFriendlyRating(2),
            "total_rating" => $this->countRating(),
            "percentageRatings" => (object) [
                "5" => $this->starRatingPercent(),
                "4" => $this->starRatingPercent(4),
                "3" => $this->starRatingPercent(3),
                "2" => $this->starRatingPercent(2),
                "1" => $this->starRatingPercent(1),
            ]

        ];
    }
}
