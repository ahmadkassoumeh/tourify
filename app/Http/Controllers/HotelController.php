<?php

namespace App\Http\Controllers;


use App\Services\RatingService;
use App\Services\FavoriteService;
use Illuminate\Http\Request;
use App\Services\BookRoomService;
use App\Models\Hotel;
use App\Models\City;
use App\Http\Resources\HotelResource;
use App\Utilities\ApiResponseService;
use Illuminate\Validation\ValidationException;
use App\Models\HotelRoom;
use DateTime;

class HotelController extends Controller
{
    public function __construct(
        protected RatingService $ratingService,
        protected FavoriteService $favoriteService,
        protected BookRoomService $bookingRoomService
    ) {}

    public function index()
    {
        $userId = auth()->id();
        $hotel = Hotel::with(['images' => function ($query) {
            $query->where('is_main', true);
        }])
            ->withAvg('ratings as average_rating', 'rating')
            ->get()        
            ->map(function ($hotel) use ($userId) {
            $hotel->is_favorite = $hotel->favorites()
                ->where('user_id', $userId)
                ->exists();
            return $hotel;
        });;

        return response()->json([
            'success' => true,
            'data' => $hotel
        ]);
    }

    public function show($id)
    {
        $userId = auth()->id();
        $hotel = Hotel::with('images','rooms')
            ->withAvg('ratings as average_rating', 'rating')
            ->withExists([
            'favorites as is_favorite' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }
            ])
            ->findOrFail($id);


        return response()->json([
            'success' => true,
            'data' => $hotel,
        ]);
    }

    public function rate(Request $request, $hotelId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $hotel = Hotel::findOrFail($hotelId);

        $this->ratingService->rate(
            $hotel,
            auth()->id(),
            $request->rating
        );

        return response()->json([
            'message' => 'Rating saved.'
        ]);
    }

    public function toggleFavorite($hotelId)
    {
        $hotel = Hotel::findOrFail($hotelId);

        $favorite = $this->favoriteService->toggle(
            $hotel,
            auth()->id()
        );

        return response()->json([
            'is_favorite' => $favorite
        ]);
    }

    public function bookRoom($roomId, Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $room = HotelRoom::findOrFail($roomId);

        $startDate = new DateTime($request->start_date);
        $endDate = new DateTime($request->end_date);

        $booking = $this->bookingRoomService->bookRoom(
            $room,
            $startDate,
            $endDate,
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
            'country_id' => ['required', 'exists:countries,id'],
            'city_id'    => ['required', 'exists:cities,id'],
        ]);

        $city = City::where('id', $request->city_id)
            ->where('country_id', $request->country_id)
            ->first();

        if (! $city) {
            throw ValidationException::withMessages([
                'city_id' => 'The selected city does not belong to the selected country.',
            ]);
        }

        $hotels = Hotel::with([
            'city.country',
            'images',
            'rooms',
        ])
            ->where('city_id', $city->id)
            ->orderBy('name')
            ->get();

        return ApiResponseService::successResponse(
            data: HotelResource::collection($hotels)
        );
    }
}
