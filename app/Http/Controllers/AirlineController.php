<?php

namespace App\Http\Controllers;

use App\Models\Airline;
use App\Models\Flight;
use App\Services\RatingService;
use App\Services\FavoriteService;
use Illuminate\Http\Request;
use App\Services\BookFlightService;
use App\Models\FlightSchedule;

class AirlineController extends Controller
{
    public function __construct(
        protected RatingService $ratingService,
        protected FavoriteService $favoriteService,
        protected BookFlightService $bookingflightService
    ) {}

    public function index()
    {
        $userId = auth()->id();
        $airlines = Airline::withAvg('ratings as average_rating', 'rating')
        ->get()
        ->map(function ($airline) use ($userId) {

            $airline->is_favorite = $airline->favorites()
                ->where('user_id', $userId)
                ->exists();

            return $airline;
        });

        return response()->json([
            'success' => true,
            'data' => $airlines
        ]);
    }

    public function flights($airlineId)
    {
        $airline = Airline::with([
            'flights.fromCity',
            'flights.toCity'
        ])->findOrFail($airlineId);

        return response()->json([
            'success' => true,
            'data' => $airline->flights
        ]);
    }

    public function schedules($flightId)
    {
        $flight = Flight::with('schedules')->findOrFail($flightId);

        return response()->json([
            'success' => true,
            'data' => $flight->schedules
        ]);
    }

    public function rate(Request $request, $airlineId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $airline = Airline::findOrFail($airlineId);

        $this->ratingService->rate(
            $airline,
            auth()->id(),
            $request->rating
        );

        return response()->json([
            'message' => 'Rating saved.'
        ]);
    }

    public function toggleFavorite($airlineId)
    {
        $airline = Airline::findOrFail($airlineId);

        $favorite = $this->favoriteService->toggle(
            $airline,
            auth()->id()
        );

        return response()->json([
            'is_favorite' => $favorite
        ]);
    }

    public function bookFlight($scheduleId)
    {
        $schedule = FlightSchedule::findOrFail($scheduleId);

        $booking = $this->bookingflightService->bookFlight(
            $schedule,
            auth()->id()
        );

        return response()->json([
            'success' => true,
            'data' => $booking
        ]);
    }

    public function dropList(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'city_id'    => 'required|exists:cities,id',
        ]);

        $airlines = Airline::whereHas('flights', function ($q) use ($request) {
            $q->where(function ($q) use ($request) {
                $q->where('from_city_id', $request->city_id)
                    ->orWhere('to_city_id', $request->city_id);
            });
        })
            ->with([
                'flights' => function ($q) use ($request) {
                    $q->where(function ($q) use ($request) {
                        $q->where('from_city_id', $request->city_id)
                            ->orWhere('to_city_id', $request->city_id);
                    })
                        ->with(['fromCity', 'toCity']);
                }
            ])
            ->get();

        return response()->json($airlines);
    }
}
