<?php

namespace App\Services;
use App\Models\User;
use App\Models\HotelRoom;
use Illuminate\Support\Facades\DB;
use DateTime;
use Exception;

class BookRoomService
{
    public function bookRoom(
        HotelRoom $room,
        DateTime $startDate,
        DateTime $endDate,
        int $userId
    )
    {
        return DB::transaction(function () use ($room, $startDate, $endDate, $userId) {

            $user = User::lockForUpdate()->findOrFail($userId);

            $room = HotelRoom::lockForUpdate()
                ->with('hotel')
                ->findOrFail($room->id);

            $hasBooking = $room->bookings()
                ->whereIn('status', ['pending', 'confirmed'])
                ->where('start_date', '<=', $endDate)
                ->where('end_date', '>=', $startDate)
                ->exists();

            $validateDates = $startDate < $endDate;

            if (!$validateDates) {
                throw new Exception('تاريخ البداية يجب أن يكون قبل تاريخ النهاية.');
            }

            if ($hasBooking) {
                throw new Exception('هذه الغرفة محجوزة بالفعل.');
            }

            $price = $room->price;

            if ($user->credit < $price) {
                throw new Exception('لا يوجد رصيد كافٍ لحجز هذه الغرفة.');
            }

            $user->decrement('credit', $price);

            $room->hotel->increment('credit', $price);

            return $room->bookings()->create([
                'user_id' => $user->id,
                'status' => 'pending',
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);
        });
    }
}