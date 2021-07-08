<?php

namespace App\Models\Appointment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OffDaySchedule extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function mechanic() {
        return $this->belongsTo(Mechanic::class, 'user_id', 'encodedKey');
    }
}
