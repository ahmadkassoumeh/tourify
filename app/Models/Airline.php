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
}
