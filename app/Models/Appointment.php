<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $dates = [
        'date'
    ];

    public function mechanic() {
        return $this->belongsTo(Mechanic::class, 'mechanic_id', 'encodedKey');
    }

    public function visitor() {
        return $this->belongsTo(User::class, 'visitor_id', 'encodedKey');
    }
}
