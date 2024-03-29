<?php

namespace App\Models;

use App\Traits\AddUUID;
use App\Models\UserProfile;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Codebyray\ReviewRateable\Models\Rating;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use App\DataTransferObjects\UserDataTransferObject;
use App\DataTransferObjects\CompanyDataTransferObject;
use App\DataTransferObjects\ProfileDataTransferObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes, AddUUID, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $dates = [
        'updated_at',
        'created_at',
        'deleted_at',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Define relationships
    public function partDealer() {
        return $this->hasOne(PartDealer::class);
    }
    public function mechanic() {
        return $this->hasOne(Mechanic::class);
    }
    public function otp() {
        return $this->hasOne(Otp::class);
    }
    public function adService() {
        return $this->hasMany(AdService::class);
    }
    public function category() {
        return $this->hasMany(Category::class);
    }
    public function getRouteKeyName()
    {
        return 'encodedKey';
    }
    public function ratings()
    {
        return $this->morphMany(Rating::class, 'author');
    }
    public function userAppointment() {
        return $this->hasMany(Appointment::class, 'visitor_id', 'encodedKey');
    }
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'owner');
    }
    public function notes() {
        return $this->hasMany(Note::class);
    }
    public function income() {
        return $this->hasMany(Income::class);
    }
    public function events() {
        return $this->hasMany(Event::class);
    }
    // Define JWT auth methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function format() {
        return [
            "firstName" => $this->first_name,
            "lastName" => $this->last_name,
            "email" => $this->email
        ];
    }

    public function setPasswordAttribute($input) {
        if($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    public function gererateOTP() {
        $this->resetOTP();
        $OTP = rand(1000, 9999);
        $expires = now()->addMinutes(10);
        return $this->otp()->create([
            'digit' => $OTP,
            'expires_at' => $expires
        ]);
    }
    public function resetOTP() {
        if($this->otp) {
            return $this->otp->delete();
        }
    }

    public function sendPasswordResetNotification($token) {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function createNewToken() {
        return rand(100000, 999999);
    }
}
