<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlightSchedule extends Model
{
    protected $fillable = [
        'flight_id',
        'date',
        'departure_time',
        'arrival_time'
    ];

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }
}
