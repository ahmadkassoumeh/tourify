<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Hotel;
use App\Models\HotelImage;
use App\Models\HotelRoom;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        $hotels = [

            [
                'city' => 'Damascus',
                'name' => 'Damascus Royal Hotel',
                'folder' => 'damascus_royal_hotel',
                'description' => 'Luxury hotel in Damascus.',
                'phone' => '0999067801',
            ],

            [
                'city' => 'Beijing',
                'name' => 'Beijing Palace Hotel',
                'folder' => 'beijing_palace_hotel',
                'description' => 'Luxury hotel in Beijing.',
                'phone' => '0999849002',
            ],

            [
                'city' => 'Rome',
                'name' => 'Rome Imperial Hotel',
                'folder' => 'rome_imperial_hotel',
                'description' => 'Luxury hotel in Rome.',
                'phone' => '0999558003',
            ],

        ];

        $cities = City::pluck('id', 'name');

        $roomTypes = [

            'A' => [
                'capacity' => 4,
                'price' => 100,
            ],

            'B' => [
                'capacity' => 3,
                'price' => 75,
            ],

            'C' => [
                'capacity' => 2,
                'price' => 50,
            ],

            'D' => [
                'capacity' => 1,
                'price' => 25,
            ],

        ];

        foreach ($hotels as $data) {

            $hotel = Hotel::create([
                'city_id' => $cities[$data['city']],
                'name' => $data['name'],
                'description' => $data['description'],
                'phone' => $data['phone'],
                'credit' => 0,
            ]);

            $sourcePath = database_path(
                "seeders/assets/hotels/{$data['folder']}"
            );

            $images = glob(
                $sourcePath . '/*.{jpg,jpeg,png}',
                GLOB_BRACE
            );

            foreach ($images as $index => $image) {

                $fileName = Str::random(20) . '.'
                    . pathinfo($image, PATHINFO_EXTENSION);

                $storagePath = "{$hotel->id}/{$fileName}";

                Storage::disk('hotel')->put(
                    $storagePath,
                    file_get_contents($image)
                );

                HotelImage::create([
                    'hotel_id' => $hotel->id,
                    'path' => $storagePath,
                    'is_main' => $index === 0,
                ]);
            }

            foreach ($roomTypes as $type => $room) {

                for ($i = 1; $i <= 50; $i++) {

                    HotelRoom::create([

                        'hotel_id' => $hotel->id,

                        'type' => $type,

                        'capacity' => $room['capacity'],

                        'price' => $room['price'],

                    ]);

                }

            }

        }

    }
}