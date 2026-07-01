<?php

namespace Database\Seeders;

use App\Enums\UserStatusEnum;
use App\Models\Agency;
use App\Models\AgencyImage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AgencySeeder extends Seeder
{
    public function run(): void
    {
        $agencies = [

            [
                'name' => 'Nawafir Travel',
                'folder' => 'nawafir_travel',
                'description' => 'Tourism office in Damascus.',
                'landline_phone' => '0112223333',
                'address' => 'Damascus - Mazzeh',
                'email' => 'nawafir@example.com',
                'phone' => '0999000001',
                'username' => 'nawafir',
            ],

            [
                'name' => 'Beijing Tours',
                'folder' => 'beijing_wangfu_international',
                'description' => 'Tourism office in Beijing.',
                'landline_phone' => '0108889999',
                'address' => 'Beijing',
                'email' => 'beijing@example.com',
                'phone' => '0999000002',
                'username' => 'beijing',
            ],

            [
                'name' => 'Rome Travel',
                'folder' => 'tour_in_rome',
                'description' => 'Tourism office in Rome.',
                'landline_phone' => '0667778888',
                'address' => 'Rome',
                'email' => 'rome@example.com',
                'phone' => '0999000003',
                'username' => 'rome',
            ],

        ];

        foreach ($agencies as $data) {

            $user = User::create([

                'username' => $data['username'],

                'first_name' => $data['name'],

                'last_name' => 'Agency',

                'email' => $data['email'],

                'phone_number' => $data['phone'],

                'password' => Hash::make('password'),

                'status' => UserStatusEnum::APPROVED,

                'credit' => 100000,

            ]);

            $user->assignRole('agency');

            $agency = Agency::create([

                'user_id' => $user->id,

                'name' => $data['name'],

                'description' => $data['description'],

                'landline_phone' => $data['landline_phone'],

                'address' => $data['address'],

            ]);

            $sourcePath = database_path(
                "seeders/assets/agencies/{$data['folder']}"
            );

            $image = glob(
                $sourcePath . '/*.{jpg,jpeg,png}',
                GLOB_BRACE
            )[0] ?? null;

            if (!$image) {
                continue;
            }

            $fileName = Str::random(20) . '.'
                . pathinfo($image, PATHINFO_EXTENSION);

            $storagePath = "{$agency->id}/{$fileName}";

            Storage::disk('agency')->put(
                $storagePath,
                file_get_contents($image)
            );

            AgencyImage::create([

                'agency_id' => $agency->id,

                'path' => $storagePath,

                'is_main' => true,

            ]);
        }
    }
}