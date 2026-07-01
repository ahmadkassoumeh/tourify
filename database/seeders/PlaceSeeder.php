<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Place;
use App\Models\PlaceImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PlaceSeeder extends Seeder
{
    public function run(): void
    {
        $places = [

            [
                'city' => 'Damascus',
                'name' => 'Umayyad Mosque',
                'folder' => 'umayyad_mosque',
                'description' => 'One of the oldest mosques in the world.',
                'history' => 'Built during the Umayyad Caliphate.',
            ],

            [
                'city' => 'Damascus',
                'name' => 'Mount Qasioun',
                'folder' => 'mount_qasioun',
                'description' => 'A mountain overlooking Damascus.',
                'history' => null,
            ],

            [
                'city' => 'Beijing',
                'name' => 'Forbidden City',
                'folder' => 'forbidden_city',
                'description' => 'Imperial palace of China.',
                'history' => 'Built in the Ming dynasty.',
            ],

            [
                'city' => 'Beijing',
                'name' => 'Summer Palace',
                'folder' => 'summer_palace',
                'description' => 'Imperial garden and lake.',
                'history' => null,
            ],

            [
                'city' => 'Rome',
                'name' => 'Colosseum',
                'folder' => 'colosseum',
                'description' => 'Ancient Roman amphitheatre.',
                'history' => 'Built in 72 AD.',
            ],

            [
                'city' => 'Rome',
                'name' => 'Villa Borghese',
                'folder' => 'villa_borghese',
                'description' => 'One of the largest public parks in Rome.',
                'history' => null,
            ],

        ];

        foreach ($places as $data) {

            $city = City::where('name', $data['city'])->first();

            $place = Place::create([
                'city_id' => $city->id,
                'name' => $data['name'],
                'description' => $data['description'],
                'history' => $data['history'],
            ]);

            $sourcePath = database_path(
                "seeders/assets/places/{$data['folder']}"
            );

            $images = glob(
                $sourcePath . '/*.{jpg,jpeg,png}',
                GLOB_BRACE
            );

            foreach ($images as $index => $image) {

                $fileName = Str::random(20) . '.'
                    . pathinfo($image, PATHINFO_EXTENSION);

                $storagePath = "{$place->id}/{$fileName}";

                Storage::disk('place')->put(
                    $storagePath,
                    file_get_contents($image)
                );

                PlaceImage::create([
                    'place_id' => $place->id,
                    'path' => $storagePath,
                    'is_main' => $index === 0,
                ]);
            }
        }
    }
}