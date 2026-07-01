<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageDay extends Model
{
    protected $fillable = [

        'package_id',

        'date',

    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function items()
    {
        return $this->hasMany(PackageDayItem::class);
    }
}
