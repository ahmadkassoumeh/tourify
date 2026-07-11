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
                'city' => 'Homs',
                'name' => 'ALHssn Castle',
                'folder' => 'alhssn_castle',
                'description' => 'A historic castle in Homs.',
                'history' => 'Built during the Crusader period.',
            ],

            [
                'city' => 'Aleppo',
                'name' => 'Aleppo Castle',
                'folder' => 'aleppo_castle',
                'description' => 'A historic castle in Aleppo.',
                'history' => 'Built during the Crusader period.',
            ],

            [
                'city' => 'Latakia',
                'name' => 'Mashqita Lakes',
                'folder' => 'mashqita_lakes',
                'description' => 'A scenic lake in Latakia.',
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
                'city' => 'Shanghai',
                'name' => 'Yu Garden',
                'folder' => 'yu_garden',
                'description' => 'the largest and oldest rockery in the southern Yangtze River region.',
                'history' => null,
            ],

            [
                'city' => 'Guangzhou',
                'name' => 'Canton Tower',
                'folder' => 'canton_tower',
                'description' => 'A modern landmark in Guangzhou.',
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

            [
                'city' => 'Venice',
                'name' => 'Piazza San Marco',
                'folder' => 'piazza_san_marco',
                'description' => 'The main square of Venice.',
                'history' => null,
            ],

            [
                'city' => 'Naples',
                'name' => 'Pompeii',
                'folder' => 'pompeii',
                'description' => 'Ancient Roman city preserved by the eruption of Mount Vesuvius.',
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