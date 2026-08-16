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
        private ?int $packageId = null;

    private function validateSameRoomType(array $data): void
    {
        $roomTypes = collect($data['days'])
            ->pluck('room_type')
            ->unique();

        if ($roomTypes->count() > 1) {
            throw ValidationException::withMessages([
                'days' => 'All hotels must use the same room type.'
            ]);
        }
    }

    private function validateFlightIsFirstAndLast(array $data)
    {
        $daysCount = count($data['days']);

        foreach ($data['days'] as $index => $day) {

            $isFirst = $index == 0;

            $isLast = $index == $daysCount - 1;

            if (
                !$isFirst &&
                !$isLast &&
                !empty($day['flight_schedule_id'])
            ) {

                throw ValidationException::withMessages([
                    'flight' => 'Flights are only allowed on first and last day.'
                ]);
            }
        }
    }

    private function calculateHotelCost(array $data): float
    {
        $total = 0;

        foreach ($data['days'] as $day) {

            $room = HotelRoom::where('hotel_id', $day['hotel_id'])
                ->where('type', $day['room_type'])
                ->firstOrFail();

            $total += $room->price * $data['quantity'];
        }

        return $total;
    }

    private function calculateFlightCost(array $data): float
    {
        $total = 0;

        foreach ($data['days'] as $day) {

            if (empty($day['flight_schedule_id'])) {
                continue;
            }

            $room = HotelRoom::where('hotel_id', $day['hotel_id'])
                ->where('type', $day['room_type'])
                ->firstOrFail();

            $flight = FlightSchedule::with('flight')
                ->findOrFail($day['flight_schedule_id']);

            $tickets = $room->capacity * $data['quantity'];

            $total += $tickets * $flight->flight->price;
        }

        return $total;
    }

    private function checkAgencyWallet(
        User $user,
        float $amount
    ): void {

        $currentBalance = $user->credit;
        $missingAmount = max(0, $amount - $currentBalance);

        if ($currentBalance < $amount) {

            throw ValidationException::withMessages([

                'wallet' => sprintf(
                    'Insufficient wallet balance. Current balance: %.2f, Required: %.2f, Missing: %.2f.',
                    $currentBalance,
                    $amount,
                    $missingAmount
                ),

            ]);
        }
    }

    private function deductWallet(
        User $user,
        float $amount,
        array $data
    ): void {

        $user->decrement('credit', $amount);

        foreach ($data['days'] as $day) {

            /*
        |-----------------------------
        | الفندق
        |-----------------------------
        */

            $room = HotelRoom::where('hotel_id', $day['hotel_id'])
                ->where('type', $day['room_type'])
                ->firstOrFail();

            $hotelAmount = $room->price * $data['quantity'];

            Hotel::find($day['hotel_id'])
                ->increment('credit', $hotelAmount);

            /*
        |-----------------------------
        | الطيران
        |-----------------------------
        */

            if (!empty($day['flight_schedule_id'])) {

                $flight = FlightSchedule::with('flight')
                    ->findOrFail($day['flight_schedule_id']);

                $tickets = $room->capacity * $data['quantity'];

                $flightAmount =
                    $tickets * $flight->flight->price;

                Airline::find(
                    $flight->flight->airline_id
                )->increment(
                    'credit',
                    $flightAmount
                );
            }
        }
    }

    public function store(StorePackageRequest $request)
    {
        $data = $request->validated();

        $package = DB::transaction(function () use ($data) {

            $this->validateSameRoomType($data);

            $this->validateFlightIsFirstAndLast($data);

            $hotelCost = $this->calculateHotelCost($data);

            $flightCost = $this->calculateFlightCost($data);

            $this->checkAgencyWallet(
                auth()->user(),
                $hotelCost + $flightCost
            );

            $package = $this->createPackage($data);

            $this->packageId = $package->id;

            $this->createPackageDays(
                $package,
                $data
            );

            $this->createBookings($package, $data);

            $this->deductWallet(
                auth()->user(),
                $hotelCost + $flightCost,
                $data
            );
            return $package;
        });

        return ApiResponseService::createdResponse(
            data: new PackageResource(
                $package->load(
                    'agency.user',
                    'country',
                    'days.items.itemable'
                )
            )
        );
    }


    private function createPackage(array $data): Package
    {
        $agency = Agency::where(
            'user_id',
            auth()->id()
        )->firstOrFail();

        return Package::create([

            'name' => $data['name'],

            'agency_id' => $agency->id,

            'country_id' => $data['country_id'],

            'description' => $data['description'] ?? null,

            'number_of_days' => count($data['days']),

            'quantity' => $data['quantity'],

            'room_type' => $data['days'][0]['room_type'] ?? null,

            'price' => $data['price'],

        ]);
    }

    private function createPackageDays(
        Package $package,
        array $data
    ): void {

        foreach ($data['days'] as $index => $day) {

            $packageDay = PackageDay::create([

                'package_id' => $package->id,

                'date' => $day['date'],

            ]);

            $this->createPackageDayItems(
                $packageDay,
                $day
            );
        }
    }

    private function createPackageDayItems(
        PackageDay $packageDay,
        array $day
    ): void {

        PackageDayItem::create([

            'package_day_id' => $packageDay->id,

            'itemable_type' => Place::class,

            'itemable_id' => $day['place_id'],

        ]);

        PackageDayItem::create([

            'package_day_id' => $packageDay->id,

            'itemable_type' => Hotel::class,

            'itemable_id' => $day['hotel_id'],

        ]);

        PackageDayItem::create([

            'package_day_id' => $packageDay->id,

            'itemable_type' => Restaurant::class,

            'itemable_id' => $day['restaurant_id'],

        ]);

        if (!empty($day['flight_schedule_id'])) {

            PackageDayItem::create([

                'package_day_id' => $packageDay->id,

                'itemable_type' => FlightSchedule::class,

                'itemable_id' => $day['flight_schedule_id'],

            ]);
        }
    }

    private function createBookings(Package $package, array $data): void
    {
        $package->load('days.items.itemable');

        $roomType = $data['days'][0]['room_type'] ?? null;

        foreach ($package->days as $day) {

            $this->createHotelBookings(
                $package,
                $day,
                $roomType
            );

            $this->createFlightBookings(
                $package,
                $day,
                $roomType
            );
        }
    }

    private function createHotelBookings(
        Package $package,
        PackageDay $day,
        string $roomType
    ): void {

        $hotel = $day->items
            ->first(
                fn($item) => $item->itemable instanceof Hotel
            );

        if (!$hotel) {
            return;
        }


        // الغرف المحجوزة بهذا التاريخ
        $reservedRooms = Booking::where('bookable_type', HotelRoom::class)
            ->where('booking_date', $day->date)
            ->pluck('bookable_id');

        // أول عدد مطلوب من الغرف المتاحة
        $rooms = HotelRoom::where('hotel_id', $hotel->itemable->id)
            ->where('type', $roomType)
            ->whereNotIn('id', $reservedRooms)
            ->take($package->quantity)
            ->get();

        if ($rooms->count() < $package->quantity) {
            throw ValidationException::withMessages([
                'hotel' => 'Not enough available rooms.'
            ]);
        }

        foreach ($rooms as $room) {

            Booking::create([

                'user_id' => auth()->id(),

                'bookable_type' => HotelRoom::class,

                'bookable_id' => $room->id,

                'booking_date' => $day->date,

                'status' => 'agency',

                'package_id' => $this->packageId,

                'package_booking_id' => null,

            ]);
        }
    }

    private function createFlightBookings(
        Package $package,
        PackageDay $day,
        string $roomType
    ): void {




        $flight = $day->items
            ->first(
                fn($item) => $item->itemable instanceof FlightSchedule
            );

        if (!$flight) {
            return;
        }

        if (
            $flight->itemable->date != $day['date']
        ) {
            throw ValidationException::withMessages([
                'flight' => 'Flight date mismatch.'
            ]);
        }

        // نفس نوع الغرفة بكل البكيج
        $capacity = match ($roomType) {
            'A' => 4,
            'B' => 3,
            'C' => 2,
            'D' => 1,
        };

        $tickets = $package->quantity * $capacity;

        $reservedSeats = Booking::where(
            'bookable_type',
            FlightSchedule::class
        )
            ->where('booking_date', $day->date)
            ->where('bookable_id', $flight->itemable->id)
            ->count();

        if (
            $reservedSeats + $tickets >
            $flight->itemable->seats
        ) {
            throw ValidationException::withMessages([
                'flight' => 'Not enough available seats.'
            ]);
        }

        for ($i = 1; $i <= $tickets; $i++) {

            Booking::create([

                'user_id' => auth()->id(),

                'bookable_type' => FlightSchedule::class,

                'bookable_id' => $flight->itemable->id,

                'booking_date' => $day->date,

                'status' => 'agency',

                'package_id' => $this->packageId,

                'package_booking_id' => null,

            ]);
        }
    }

    public function hint(array $data)
    {
        $hotelTotalCost = $this->calculateHotelCost($data);

        $flightTotalCost = $this->calculateFlightCost($data);

        $totalCost = $hotelTotalCost + $flightTotalCost;

        $hotelCostPerPackage = round(
            $hotelTotalCost / $data['quantity'],
            2
        );

        $flightCostPerPackage = round(
            $flightTotalCost / $data['quantity'],
            2
        );

        $packageCost = round(
            $totalCost / $data['quantity'],
            2
        );

        return ApiResponseService::successResponse([

            // التكلفة الكلية على المكتب
            'total_cost' => round($totalCost, 2),

            'hotel_total_cost' => round($hotelTotalCost, 2),

            'flight_total_cost' => round($flightTotalCost, 2),

            // تكلفة الباقة الواحدة
            'costWithoutProfit' => $packageCost,

            // تفاصيل تكلفة الباقة الواحدة
            'hotel_cost_per_package' => $hotelCostPerPackage,

            'flight_cost_per_package' => $flightCostPerPackage,

            'suggested_min_price' => $packageCost * 1.15,
        ]);
    }
}
