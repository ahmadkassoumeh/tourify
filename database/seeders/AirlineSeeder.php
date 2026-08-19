<?php

namespace Database\Seeders;

use App\Enums\UserStatusEnum;
use App\Models\Airline;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AirlineSeeder extends Seeder
{
    public function run(): void
    {
        $airlines = [

            [
                'name' => 'Syrian Air',
                'username' => 'syrian_air',
                'email' => 'syrian.air@example.com',
                'phone_number' => '0991000001',
                'first_name' => 'Syrian',
                'last_name' => 'Air',
            ],

            [
                'name' => 'Air China',
                'username' => 'air_china',
                'email' => 'air.china@example.com',
                'phone_number' => '0991000002',
                'first_name' => 'Air',
                'last_name' => 'China',
            ],

            [
                'name' => 'ITA Airways',
                'username' => 'ita_airways',
                'email' => 'ita.airways@example.com',
                'phone_number' => '0991000003',
                'first_name' => 'ITA',
                'last_name' => 'Airways',
            ],

        ];

        foreach ($airlines as $data) {

            // إنشاء مستخدم شركة الطيران
            $user = User::create([

                'username' => $data['username'],

                'first_name' => $data['first_name'],

                'last_name' => $data['last_name'],

                'email' => $data['email'],

                'phone_number' => $data['phone_number'],

                'password' => Hash::make('password'),

                'status' => UserStatusEnum::APPROVED,

                'credit' => 100000,

            ]);

            // إعطاء المستخدم Role الطيران
            $user->assignRole('airline');

            // إنشاء كيان شركة الطيران
            Airline::create([

                'user_id' => $user->id,

                'name' => $data['name'],

                'credit' => 100000,

            ]);
        }
    }
}