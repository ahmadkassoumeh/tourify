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
                'city' => 'Damascus',
                'name' => 'Naranj Restaurant',
                'folder' => 'naranj_restaurant',
                'description' => 'Traditional Damascene cuisine.',
                'phone' => '0988000001',
            ],

            [
                'city' => 'Aleppo',
                'name' => 'Sissi House Restaurant',
                'folder' => 'sissi_house_restaurant',
                'description' => 'Authentic Aleppine cuisine.',
                'phone' => '0988000002',
            ],

            [
                'city' => 'Homs',
                'name' => 'Beit Al Karam',
                'folder' => 'beit_al_karam',
                'description' => 'Traditional Syrian dishes.',
                'phone' => '0988000003',
            ],

            [
                'city' => 'Beijing',
                'name' => 'Golden Dragon Restaurant',
                'folder' => 'golden_dragon_restaurant',
                'description' => 'Traditional Chinese food.',
                'phone' => '0988000002',
            ],
            [
                'city' => 'Beijing',
                'name' => 'Quanjude Roast Duck',
                'folder' => 'quanjude_roast_duck',
                'description' => 'Famous for authentic Peking Duck.',
                'phone' => '0988000008',
            ],
            [
                'city' => 'Shanghai',
                'name' => 'Din Tai Fung',
                'folder' => 'din_tai_fung_shanghai',
                'description' => 'Famous dumplings and Taiwanese cuisine.',
                'phone' => '0988000009',
            ],
            [
                'city' => 'Guangzhou',
                'name' => 'Bingsheng Mansion',
                'folder' => 'bingsheng_mansion',
                'description' => 'Traditional Cantonese cuisine.',
                'phone' => '0988000010',
            ],

            [
                'city' => 'Rome',
                'name' => 'La Piazza Restaurant',
                'folder' => 'la_piazza_restaurant',
                'description' => 'Italian cuisine.',
                'phone' => '0988000003',
            ],

            [
                'city' => 'Rome',
                'name' => 'La Pergola',
                'folder' => 'la_pergola',
                'description' => 'Fine Italian dining.',
                'phone' => '0988000015',
            ],
            [
                'city' => 'Milan',
                'name' => 'Cracco',
                'folder' => 'cracco',
                'description' => 'Modern Italian cuisine.',
                'phone' => '0988000016',
            ],
            [
                'city' => 'Florence',
                'name' => 'La Giostra',
                'folder' => 'la_giostra',
                'description' => 'Traditional Tuscan dishes.',
                'phone' => '0988000017',
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
