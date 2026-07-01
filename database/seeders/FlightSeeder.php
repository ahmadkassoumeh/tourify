<?php

namespace Database\Seeders;

use App\Models\Airline;
use App\Models\City;
use App\Models\Flight;
use App\Models\FlightSchedule;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FlightSeeder extends Seeder
{
    public function run(): void
    {

        $damascus = City::where('name','Damascus')->first();
        $beijing  = City::where('name','Beijing')->first();
        $rome     = City::where('name','Rome')->first();

        $syrian = Airline::where('name','Syrian Air')->first();
        $china  = Airline::where('name','Air China')->first();
        $italy  = Airline::where('name','ITA Airways')->first();

        $flights = [

            [$syrian->id,$damascus->id,$beijing->id,450],

            [$china->id,$beijing->id,$damascus->id,450],

            [$syrian->id,$damascus->id,$rome->id,300],

            [$italy->id,$rome->id,$damascus->id,300],

            [$italy->id,$rome->id,$beijing->id,500],

            [$china->id,$beijing->id,$rome->id,500],

        ];

        foreach($flights as $index=>$data){

            $flight = Flight::create([

                'airline_id'=>$data[0],

                'from_city_id'=>$data[1],

                'to_city_id'=>$data[2],

                'price'=>$data[3],

            ]);

            $days = $index < 3
                ? [0,2,4]   // الأحد الثلاثاء الخميس
                : [6,1,3];  // السبت الاثنين الأربعاء

            for($i=0;$i<60;$i++){

                $date = Carbon::today()->addDays($i);

                if(in_array($date->dayOfWeek,$days)){

                    FlightSchedule::create([

                        'flight_id'=>$flight->id,

                        'date'=>$date,

                        'departure_time'=>'08:00:00',

                        'arrival_time'=>'16:00:00',

                    ]);

                }

            }

        }

    }
}