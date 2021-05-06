<?php

namespace App\Models\Appointment;

use App\Models\Mechanic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingHour extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function mechanic() {
        return $this->belongsTo(Mechanic::class, 'user_id', 'encodedKey');
    }
}
