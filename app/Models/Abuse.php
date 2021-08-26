<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abuse extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the parent abusable model (product or review).
     */
    public function abusable()
    {
        return $this->morphTo();
    }

    public function format()
    {
        return [
            'id'            => $this->id,
            'full_name'     => $this->full_name,
            'email'         => $this->email,
            'message'       => $this->message,
            "product_id"    => $this->abusable->encodedKey,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at
        ];
    }
}
