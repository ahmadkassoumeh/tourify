<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelRoom extends Model
{
    protected $fillable = [
        'hotel_id',
        'type',
        'capacity',
        'price'
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
