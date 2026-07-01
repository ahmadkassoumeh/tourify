<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [

        'agency_id',

        'country_id',

        'name',

        'description',

        'number_of_days',

        'quantity',

        'price',

    ];

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function days()
    {
        return $this->hasMany(PackageDay::class);
    }
}
