<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $syria = Country::where('name', 'Syria')->first();
        $china = Country::where('name', 'China')->first();
        $italy = Country::where('name', 'Italy')->first();

        $cities = [

            // Syria
            ['country_id' => $syria->id, 'name' => 'Damascus'],
            ['country_id' => $syria->id, 'name' => 'Aleppo'],
            ['country_id' => $syria->id, 'name' => 'Homs'],
            ['country_id' => $syria->id, 'name' => 'Latakia'],
            ['country_id' => $syria->id, 'name' => 'Tartus'],
            ['country_id' => $syria->id, 'name' => 'Palmyra'],

            // China
            ['country_id' => $china->id, 'name' => 'Beijing'],
            ['country_id' => $china->id, 'name' => 'Shanghai'],
            ['country_id' => $china->id, 'name' => 'Guangzhou'],
            ['country_id' => $china->id, 'name' => 'Shenzhen'],
            ['country_id' => $china->id, 'name' => 'Luoyang'],
            ['country_id' => $china->id, 'name' => 'Nanjing'],

            // Italy
            ['country_id' => $italy->id, 'name' => 'Rome'],
            ['country_id' => $italy->id, 'name' => 'Milan'],
            ['country_id' => $italy->id, 'name' => 'Venice'],
            ['country_id' => $italy->id, 'name' => 'Florence'],
            ['country_id' => $italy->id, 'name' => 'Naples'],
            ['country_id' => $italy->id, 'name' => 'Turin'],
        ];

        foreach ($cities as $city) {
            City::create($city);
        }
    }
}