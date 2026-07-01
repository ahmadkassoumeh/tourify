<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Restaurant;
use App\Models\RestaurantImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RestaurantSeeder extends Seeder
{
    public function run(): void
    {
        $restaurants = [

            [
                'city' => 'Damascus',
                'name' => 'Al Shami Restaurant',
                'folder' => 'al_shami_restaurant',
                'description' => 'Traditional Syrian food.',
                'phone' => '0988000001',
            ],

            [
                'city' => 'Beijing',
                'name' => 'Golden Dragon Restaurant',
                'folder' => 'golden_dragon_restaurant',
                'description' => 'Traditional Chinese food.',
                'phone' => '0988000002',
            ],

            [
                'city' => 'Rome',
                'name' => 'La Piazza Restaurant',
                'folder' => 'la_piazza_restaurant',
                'description' => 'Italian cuisine.',
                'phone' => '0988000003',
            ],

        ];

        $cities = City::pluck('id', 'name');

        foreach ($restaurants as $data) {

            $restaurant = Restaurant::create([
                'city_id' => $cities[$data['city']],
                'name' => $data['name'],
                'description' => $data['description'],
                'phone' => $data['phone'],
            ]);

            $sourcePath = database_path(
                "seeders/assets/restaurants/{$data['folder']}"
            );

            $images = glob(
                $sourcePath . '/*.{jpg,jpeg,png}',
                GLOB_BRACE
            );

            foreach ($images as $index => $image) {

                $fileName = Str::random(20) . '.'
                    . pathinfo($image, PATHINFO_EXTENSION);

                $storagePath = "{$restaurant->id}/{$fileName}";

                Storage::disk('restaurant')->put(
                    $storagePath,
                    file_get_contents($image)
                );

                RestaurantImage::create([
                    'restaurant_id' => $restaurant->id,
                    'path' => $storagePath,
                    'is_main' => $index === 0,
                ]);
            }

        }
    }
}