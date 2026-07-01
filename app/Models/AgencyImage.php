<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgencyImage extends Model
{
    protected $fillable = [
        'agency_id',
        'path',
        'is_main'
    ];

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
}
