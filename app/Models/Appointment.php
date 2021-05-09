<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function mechanic() {
        return $this->belongsTo(Mechanic::class, 'mechanic_id', 'encodedKey');
    }

    public function visitor() {
        return $this->belongsTo(User::class, 'visitor_id', 'encodedKey');
    }
}
