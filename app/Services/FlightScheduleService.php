<?php

namespace App\Services;

use App\Models\Flight;
use App\Models\FlightSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FlightScheduleService
{
    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {

            $airline = auth()->user()->airline;

            if (!$airline) {
                throw ValidationException::withMessages([
                    'airline' => 'Airline profile not found.'
                ]);
            }

            $flight = Flight::where('id', $data['flight_id'])
                ->where('airline_id', $airline->id)
                ->first();

            if (!$flight) {
                throw ValidationException::withMessages([
                    'flight_id' => 'This flight does not belong to your airline.'
                ]);
            }

            $startDate = Carbon::parse($data['start_date']);

            $weeks = $data['weeks'];

            $daysOfWeek = $data['days_of_week'];

            $schedules = [];

            for ($week = 0; $week < $weeks; $week++) {

                $weekStart = $startDate
                    ->copy()
                    ->startOfWeek(Carbon::SUNDAY)
                    ->addWeeks($week);

                foreach ($daysOfWeek as $dayOfWeek) {

                    $date = $weekStart
                        ->copy()
                        ->addDays($dayOfWeek);

                    if ($date->lt($startDate)) {
                        continue;
                    }

                    // منع وجود أكثر من Schedule لنفس الرحلة بنفس اليوم
                    $exists = FlightSchedule::where(
                            'flight_id',
                            $flight->id
                        )
                        ->whereDate('date', $date)
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    $schedules[] = FlightSchedule::create([

                        'flight_id' => $flight->id,

                        'date' => $date->toDateString(),

                        'departure_time' =>
                            $data['departure_time'],

                        'arrival_time' =>
                            $data['arrival_time'],

                    ]);
                }
            }

            return $schedules;
        });
    }
}