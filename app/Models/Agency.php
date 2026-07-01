<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'credit',
        'landline_phone',
        'address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(AgencyImage::class);
    }

    public function packages()
    {
        return $this->hasMany(Package::class);
    }
}
