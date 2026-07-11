<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'booking_date',
        'start_date',
        'end_date',
        'status',
        'package_booking_id',
        'tickets_count',
        'bookable_id',
        'bookable_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookable()
    {
        return $this->morphTo();
    }

    public function packageBooking()
    {
        return $this->belongsTo(
            Booking::class,
            'package_booking_id'
        );
    }

    public function children()
    {
        return $this->hasMany(
            Booking::class,
            'package_booking_id'
        );
    }
}
