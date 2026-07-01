<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {

        $countries = [

            'Syria',

            'China',

            'Italy'

        ];

        foreach ($countries as $countryName) {

            $country = Country::create([

                'name' => $countryName,

                'flag' => '',

            ]);

            $sourcePath = database_path(
                "seeders/assets/countries/$countryName"
            );

            $image = glob(
                $sourcePath . '/*.{png,jpg,jpeg}',
                GLOB_BRACE
            )[0];

            $fileName = Str::random(20) . '.' . pathinfo(
                $image,
                PATHINFO_EXTENSION
            );

            $storagePath = $country->id . '/' . $fileName;

            Storage::disk('country')->put(

                $storagePath,

                file_get_contents($image)

            );

            $country->update([

                'flag' => $storagePath

            ]);
        }
    }
}
