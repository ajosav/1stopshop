<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecordViewContact extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function partDealer() {
        return $this->belongsTo(PartDealer::class, 'owner_id', 'encodedKey');
    }

    public function product() {
        return $this->belongsTo(AdService::class, 'product_id', 'encodedKey');
    }
}
