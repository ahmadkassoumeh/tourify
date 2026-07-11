<?php

namespace App\Services;
use App\Models\User;
use App\Models\FlightSchedule;
use Illuminate\Support\Facades\DB;
use Exception;

class BookFlightService
{
    public function bookFlight(
        FlightSchedule $schedule,
        int $userId
    )
    {
        return DB::transaction(function () use ($schedule, $userId) {

            
            $user = User::lockForUpdate()->findOrFail($userId);

        
            $schedule = FlightSchedule::lockForUpdate()
                ->with('flight')
                ->findOrFail($schedule->id);

            
            $confirmedBookings = $schedule->bookings()
                ->whereIn('status', ['pending', 'confirmed'])
                ->count();

            if ($confirmedBookings >= $schedule->seats) {
                throw new Exception('لا يوجد مقاعد متاحة في هذه الرحلة');
            }

            
            $price = $schedule->flight->price;

            
            if ($user->credit < $price) {
                throw new Exception('لا يوجد رصيد كافي في حسابك لحجز هذه الرحلة');
            }

            
            $user->decrement('credit', $price);

            $schedule->flight->airline->increment('credit', $price);
            
            return $schedule->bookings()->create([
                'user_id' => $user->id,
                'booking_date' => $schedule->date,
                'status' => 'pending',
            ]);
        });
    }
}