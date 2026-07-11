<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = [
        'user_id',
        'rateable_id',
        'rateable_type',
        'rating',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * (Place ,Restaurant ,Hotel ,Airline ,Agency)
     */
    public function rateable()
    {
        return $this->morphTo();
    }
}
