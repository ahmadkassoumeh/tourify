<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    protected $fillable = [
        'airline_id',
        'from_city_id',
        'to_city_id',
        'price'
    ];

    public function airline()
    {
        return $this->belongsTo(Airline::class);
    }

    public function fromCity()
    {
        return $this->belongsTo(City::class, 'from_city_id');
    }

    public function toCity()
    {
        return $this->belongsTo(City::class, 'to_city_id');
    }

    public function schedules()
    {
        return $this->hasMany(FlightSchedule::class);
    }
}
