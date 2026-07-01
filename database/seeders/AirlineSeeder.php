<?php

namespace Database\Seeders;

use App\Models\Airline;
use Illuminate\Database\Seeder;

class AirlineSeeder extends Seeder
{
    public function run(): void
    {
        Airline::insert([

            ['name'=>'Syrian Air'],

            ['name'=>'Air China'],

            ['name'=>'ITA Airways'],

        ]);
    }
}