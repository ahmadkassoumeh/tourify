<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'name',
    ];

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function places()
    {
        return $this->hasManyThrough(
            Place::class,
            City::class
        );
    }

    public function hotels()
    {
        return $this->hasManyThrough(
            Hotel::class,
            City::class
        );
    }

    public function restaurants()
    {
        return $this->hasManyThrough(
            Restaurant::class,
            City::class
        );
    }

    public function packages()
    {
        return $this->hasMany(Package::class);
    }
}
