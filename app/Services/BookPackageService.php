<?php

namespace App\Services;

use App\Models\Agency;
use App\Models\Package;
use App\Models\PackageDay;
use App\Models\PackageDayItem;
use App\Models\Place;
use App\Models\HotelRoom;
use App\Models\Restaurant;
use App\Models\FlightSchedule;
use App\Http\Requests\StorePackageRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\User;
use App\Models\Airline;
use App\Models\City;
use App\Utilities\ApiResponseService;
use App\Http\Resources\PackageResource;
use Illuminate\Validation\ValidationException;


class PackageService
{
    // وقت زبون يشتري الباقة
    public function bookPackage(Package $package, User $customer): void
    {
        DB::transaction(function () use ($package, $customer) {

            // 1. تحقق إنو في مقاعد متاحة
            $soldCount = Booking::where('bookable_type', Package::class)
                ->where('bookable_id', $package->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->count();


            if ($soldCount >= $package->quantity) {
                throw ValidationException::withMessages([
                    'package' => 'No available seats in this package.'
                ]);
            }

            // 2. أنشئ الـ parent booking (Package)
            $packageBooking = Booking::create([
                'user_id'          => $customer->id,
                'bookable_type'    => Package::class,
                'bookable_id'      => $package->id,
                'booking_date'     => $package->days->first()->date,
                'status'           => 'pending',
                'package_booking_id' => null,
            ]);

            // 3. أضف children للفندق لكل يوم
            foreach ($package->days as $day) {

                $hotel = $day->items
                    ->first(fn($i) => $i->itemable instanceof Hotel);

                if ($hotel) {
                    Booking::create([
                        'user_id'            => $customer->id,
                        'bookable_type'      => HotelRoom::class,
                        'bookable_id'        => $this->assignRoom($hotel, $day),
                        'booking_date'       => $day->date,
                        'status'             => 'pending',
                        'package_booking_id' => $packageBooking->id,
                    ]);
                }

                // 4. أضف children للطيران
                $flight = $day->items
                    ->first(fn($i) => $i->itemable instanceof FlightSchedule);

                if ($flight) {
                    Booking::create([
                        'user_id'            => $customer->id,
                        'bookable_type'      => FlightSchedule::class,
                        'bookable_id'        => $flight->itemable->id,
                        'booking_date'       => $day->date,
                        'status'             => 'pending',
                        'package_booking_id' => $packageBooking->id,
                    ]);
                }
            }

            // 5. اقتطع من محفظة الزبون
            if ($customer->credit < $package->price) {
                throw ValidationException::withMessages([
                    'wallet' => 'Insufficient balance.'
                ]);
            }

            $customer->decrement('credit', $package->price);

            // 6. أضف للمكتب
            $package->agency->user->increment('credit', $package->price);
        });
    }



    //*********************************************************** *****************************************************************/


    private function assignRoom(PackageDayItem $hotelItem, PackageDay $day): int
    {
        $hotel = $hotelItem->itemable;

        // جيب IDs الغرف اللي المكتب حجزها بهاد الفندق وهاد التاريخ
        $agencyRoomIds = Booking::where('bookable_type', HotelRoom::class)
            ->where('booking_date', $day->date)
            ->where('status', 'agency')
            ->pluck('bookable_id');

        // تحقق إنها فعلاً من هاد الفندق وجيب النوع
        $agencyRoom = HotelRoom::where('hotel_id', $hotel->id)
            ->whereIn('id', $agencyRoomIds)
            ->first();

        if (!$agencyRoom) {
            throw ValidationException::withMessages([
                'hotel' => "No agency reservation found for hotel {$hotel->id}."
            ]);
        }

        $roomType = $agencyRoom->type;

        // الغرف المحجوزة بأي status بهاد التاريخ
        $reservedRoomIds = Booking::where('bookable_type', HotelRoom::class)
            ->where('booking_date', $day->date)
            ->whereIn('status', ['agency', 'pending', 'confirmed'])
            ->pluck('bookable_id');

        // أول غرفة متاحة من نفس النوع
        $room = HotelRoom::where('hotel_id', $hotel->id)
            ->where('type', $roomType)
            ->whereNotIn('id', $reservedRoomIds)
            ->first();

        if (!$room) {
            throw ValidationException::withMessages([
                'hotel' => "No available rooms in hotel {$hotel->id} on {$day->date}."
            ]);
        }

        return $room->id;
    }
}
