<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageDayItem extends Model
{
    protected $fillable = [

        'package_day_id',
            'itemable_type',
                'itemable_id',

    ];

    public function packageDay()
    {
        return $this->belongsTo(PackageDay::class);
    }

    public function itemable()
    {
        return $this->morphTo();
    }
}
