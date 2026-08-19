<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFlightScheduleRequest;
use App\Models\Flight;
use App\Models\FlightSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\FlightScheduleService;
use App\Utilities\ApiResponseService;

class FlightScheduleController extends Controller
{
    public function __construct(
        private FlightScheduleService $flightScheduleService
    ) {}

    public function store(StoreFlightScheduleRequest $request)
    {
        $data = $request->validated();

        $schedules = $this->flightScheduleService->store($data);

        return ApiResponseService::createdResponse(
            data: $schedules,
            msg: 'Flight schedules created successfully.'
        );
    }

    public function getFlightsDropdown(Request $request)
    {
        $user = $request->user();

        // جلب الـ Airline الخاصة بالمستخدم
        $airline = $user->airline;

        if (!$airline) {
            return response()->json([
                'success' => false,
                'message' => 'Airline profile not found for this user.'
            ], 404);
        }

        // جلب كل الـ Flights التابعة للـ Airline
        $flights = Flight::with(['fromCity', 'toCity'])
            ->where('airline_id', $airline->id)
            ->orderBy('id', 'desc')
            ->get();

        // تنسيق البيانات للـ Dropdown
        $formattedFlights = $flights->map(function ($flight) {
            return [
                'id' => $flight->id,
                'text' => "{$flight->fromCity->name} → {$flight->toCity->name} (\${$flight->price})",
                'from_city' => $flight->fromCity->name,
                'to_city' => $flight->toCity->name,
                'price' => $flight->price,
                'from_city_id' => $flight->from_city_id,
                'to_city_id' => $flight->to_city_id,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedFlights,
        ]);
    }
}


//
