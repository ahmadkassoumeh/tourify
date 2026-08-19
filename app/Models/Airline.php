<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Airline extends Model
{
    protected $fillable = [
        'name',
        'credit',
    ];

    public function flights()
    {
        return $this->hasMany(Flight::class);
    }

    public function ratings()
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoriteable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
