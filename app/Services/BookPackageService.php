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
use App\Http\Resources\BookingResource;


class BookPackageService
{
    // وقت زبون يشتري الباقة
    public function bookPackage(Package $package, User $customer): void
    {
        DB::transaction(function () use ($package, $customer) {

            // 1. تحقق من عدد الباقات المباعة
            $soldCount = Booking::where('bookable_type', Package::class)
                ->where('bookable_id', $package->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->count();

            if ($soldCount >= $package->quantity) {
                throw ValidationException::withMessages([
                    'package' => 'No available seats in this package.'
                ]);
            }

            // 2. تحقق من رصيد الزبون
            if ($customer->credit < $package->price) {
                throw ValidationException::withMessages([
                    'wallet' => 'Insufficient balance.'
                ]);
            }

            // 3. أنشئ Parent Booking للباقة
            $packageBooking = Booking::create([
                'user_id'             => $customer->id,
                'bookable_type'       => Package::class,
                'bookable_id'         => $package->id,
                'booking_date'        => $package->days->first()->date,
                'status'               => 'pending',
                'package_booking_id' => null,
            ]);

            // 4. لكل يوم
            foreach ($package->days as $day) {


                /*
            |--------------------------------------------------------------------------
            | Hotel
            |--------------------------------------------------------------------------
            */

                $hotel = $day->items
                    ->first(
                        fn($item) => $item->itemable instanceof Hotel
                    );
                // dd($hotel->toArray());

                if ($hotel) {

                    $this->assignAgencyRoomToCustomer(
                        packageBooking: $packageBooking,
                        hotelItem: $hotel,
                        day: $day,
                        customer: $customer,
                        package: $package,
                    );
                }

                /*
            |--------------------------------------------------------------------------
            | Flight
            |--------------------------------------------------------------------------
            */

                $flight = $day->items
                    ->first(fn($i) => $i->itemable instanceof FlightSchedule);

                if ($flight) {

                    $roomType = $package->room_type;

                    $this->assignFlightTickets(
                        $flight,
                        $day,
                        $roomType,
                        $packageBooking,
                        $package->agency->user_id,
                        $package
                    );
                }
            }

            // 5. اقتطع سعر الباقة من الزبون
            $customer->decrement(
                'credit',
                $package->price
            );

            // 6. أضف السعر إلى محفظة المكتب
            $package->agency
                ->user
                ->increment(
                    'credit',
                    $package->price
                );
        });
    }



    //*********************************************************** *****************************************************************/


    private function assignAgencyRoomToCustomer(
        Booking $packageBooking,
        PackageDayItem $hotelItem,
        PackageDay $day,
        User $customer,
        Package $package,

    ): void {

        $hotel = $hotelItem->itemable;

        /*
    |--------------------------------------------------------------------------
    | جيب حجوزات الغرف التي حجزها المكتب مسبقاً
    |--------------------------------------------------------------------------
    */

        $agencyBooking = Booking::where(
            'bookable_type',
            HotelRoom::class
        )
            ->where('package_id', $package->id)
            ->where(
                'booking_date',
                $day->date
            )
            ->where(
                'status',
                'agency'
            )
            ->whereHasMorph(
                'bookable',
                [HotelRoom::class],
                function ($query) use ($hotel) {
                    $query->where('hotel_id', $hotel->id);
                }
            )
            ->first();

        if (!$agencyBooking) {

            throw ValidationException::withMessages([
                'hotel' =>
                "No available agency room for hotel {$hotel->id} on {$day->date}."
            ]);
        }

        /*
    |--------------------------------------------------------------------------
    | حول حجز المكتب إلى حجز الزبون
    |--------------------------------------------------------------------------
    */

        $agencyBooking->update([
            'user_id'             => $customer->id,
            'status'              => 'pending',
            'package_booking_id' => $packageBooking->id,
        ]);
    }


    private function assignFlightTickets(
        PackageDayItem $flightItem,
        PackageDay $day,
        string $roomType,
        Booking $packageBooking,
        int $agencyUserId,
        Package $package

    ): void {

        $ticketsCount = match ($roomType) {
            'A' => 4,
            'B' => 3,
            'C' => 2,
            'D' => 1,
            default => throw ValidationException::withMessages([
                'room_type' => 'Invalid room type.'
            ]),
        };

        $flightSchedule = $flightItem->itemable;

        $agencyBookings = Booking::where(
            'bookable_type',
            FlightSchedule::class
        )
            ->where(
                'bookable_id',
                $flightSchedule->id
            )
            ->where('booking_date', $day->date)
            ->where('status', 'agency')
            ->where('user_id', $agencyUserId)
            ->where('package_id', $package->id)
            ->lockForUpdate()
            ->take($ticketsCount)
            ->get();

        if ($agencyBookings->count() < $ticketsCount) {

            throw ValidationException::withMessages([
                'flight' => "Not enough agency tickets available."
            ]);
        }

        foreach ($agencyBookings as $booking) {

            $booking->update([
                'user_id' => $packageBooking->user_id,
                'status' => 'pending',
                'package_booking_id' => $packageBooking->id,
            ]);
        }
    }

    ////&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&

    public function activePackages(User $agencyUser)
    {
        $packages = Package::query()
            ->whereHas('agency', function ($query) use ($agencyUser) {
                $query->where('user_id', $agencyUser->id);
            })
            ->whereHas('days', function ($query) {
                $query->where('date', '>=', today());
            })
            ->with([
                'country',
                'days.items.itemable',
            ])
            ->withCount([
                'bookings as pending_count' => function ($query) {
                    $query->where('bookable_type', Package::class)
                        ->where('status', 'pending');
                },

                'bookings as confirmed_count' => function ($query) {
                    $query->where('bookable_type', Package::class)
                        ->where('status', 'confirmed');
                },
            ])
            ->get();

        return $packages->map(function ($package) {

            $availableCount = $package->quantity - $package->confirmed_count;

            return [
                'package' => $package,
                'quantity' => $package->quantity,
                'pending_count' => $package->pending_count,
                'confirmed_count' => $package->confirmed_count,
                'available_count' => $availableCount,
            ];
        });
    }

    public function pendingBookings(Package $package)
    {
        $agency = Agency::where('user_id', auth()->user()->id)->first();

        if (!$agency || $package->agency_id !== $agency->id) {
            return ApiResponseService::unauthorizedResponse(
                msg: 'You are not authorized to access this package.'
            );
        }
        $bookings = Booking::with('user')
            ->where('bookable_type', Package::class)
            ->where('bookable_id', $package->id)
            ->where('status', 'pending')
            ->latest()
            ->get();

        return ApiResponseService::successResponse(
            data: BookingResource::collection($bookings)
        );
    }

    public function approveBooking(
        Package $package,
        Booking $packageBooking
    ): void {

        DB::transaction(function () use ($package, $packageBooking) {

            // تأكد أن الطلب فعلاً تابع لهذه الباقة
            if (
                $packageBooking->bookable_type !== Package::class ||
                $packageBooking->bookable_id !== $package->id
            ) {
                throw ValidationException::withMessages([
                    'booking' => 'This booking does not belong to this package.'
                ]);
            }

            // فقط الطلبات المعلقة يمكن قبولها
            if ($packageBooking->status !== 'pending') {
                throw ValidationException::withMessages([
                    'booking' => 'This booking is not pending.'
                ]);
            }

            // نجيب كل حجوزات الفندق والطيران الخاصة بهذا الزبون
            $children = Booking::where(
                'package_booking_id',
                $packageBooking->id
            )
                ->whereIn('bookable_type', [
                    HotelRoom::class,
                    FlightSchedule::class,
                ])
                ->lockForUpdate()
                ->get();

            // حول الـ Parent إلى confirmed
            $packageBooking->update([
                'status' => 'confirmed',
            ]);

            // وحول كل العناصر التابعة له
            $children->each(function (Booking $booking) {
                $booking->update([
                    'status' => 'confirmed',
                ]);
            });
        });
    }

    public function rejectBooking(
        Package $package,
        Booking $packageBooking
    ): void {

        DB::transaction(function () use ($package, $packageBooking) {

            // تأكد أن الحجز تابع لهذه الباقة
            if (
                $packageBooking->bookable_type !== Package::class ||
                $packageBooking->bookable_id !== $package->id
            ) {
                throw ValidationException::withMessages([
                    'booking' => 'This booking does not belong to this package.'
                ]);
            }

            // فقط الطلبات المعلقة يمكن رفضها
            if ($packageBooking->status !== 'pending') {
                throw ValidationException::withMessages([
                    'booking' => 'This booking is not pending.'
                ]);
            }

            // جيب كل حجوزات الفندق والطيران الخاصة بالزبون
            $children = Booking::where(
                'package_booking_id',
                $packageBooking->id
            )
                ->whereIn('bookable_type', [
                    HotelRoom::class,
                    FlightSchedule::class,
                ])
                ->lockForUpdate()
                ->get();

            // رفض الـ Parent
            $packageBooking->update([
                'status' => 'rejected',
            ]);

            // رفض كل العناصر التابعة
            $children->each(function (Booking $booking) {
                $booking->update([
                    'status' => 'rejected',
                ]);
            });

            // إعادة المبلغ للزبون
            $packageBooking->user->increment(
                'credit',
                $package->price
            );

            // وطرح المبلغ من رصيد المكتب
            $package->agency->user->decrement(
                'credit',
                $package->price
            );
        });
    }

    //!!!!!!!!!!!!!!! زبزن يلغي 

    public function cancelPendingBooking(
        Package $package,
        Booking $packageBooking,
        User $customer
    ): void {

        DB::transaction(function () use (
            $package,
            $packageBooking,
            $customer
        ) {

            // 1. تأكد أن هذا هو Parent Booking للـ Package
            if (
                $packageBooking->bookable_type !== Package::class ||
                $packageBooking->bookable_id !== $package->id
            ) {
                throw ValidationException::withMessages([
                    'booking' => 'This booking does not belong to this package.'
                ]);
            }

            // 2. تأكد أن الحجز للزبون الحالي
            if ($packageBooking->user_id !== $customer->id) {
                throw ValidationException::withMessages([
                    'booking' => 'You are not allowed to cancel this booking.'
                ]);
            }

            // 3. يمكن الإلغاء فقط إذا كان Pending
            if ($packageBooking->status !== 'pending') {
                throw ValidationException::withMessages([
                    'booking' => 'Only pending bookings can be cancelled.'
                ]);
            }

            // 4. جيب كل حجوزات الفندق والطيران التابعة لهذا الطلب
            $children = Booking::where(
                'package_booking_id',
                $packageBooking->id
            )
                ->whereIn('bookable_type', [
                    HotelRoom::class,
                    FlightSchedule::class,
                ])
                ->lockForUpdate()
                ->get();

            // 5. رجّع حجوزات الفندق والطيران إلى المكتب
            foreach ($children as $booking) {

                $booking->update([
                    'user_id' => $package->agency->user_id,
                    'status' => 'agency',
                    'package_booking_id' => null,
                ]);
            }

            // 6. رجّع مبلغ الباقة للزبون
            $customer->increment(
                'credit',
                $package->price
            );

            // 7. اطرح المبلغ من محفظة المكتب
            $package->agency->user->decrement(
                'credit',
                $package->price
            );

            // 8. احذف Parent Booking تبع الـ Package
            $packageBooking->delete();
        });
    }
    
}
