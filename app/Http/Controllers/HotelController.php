<?php

namespace App\Http\Controllers;


use App\Services\RatingService;
use App\Services\FavoriteService;
use Illuminate\Http\Request;
use App\Services\BookRoomService;
use App\Models\Hotel;
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
        $hotel = Hotel::with(['images' => function ($query) 
        {
            $query->where('is_main', true);
        }])
            ->withAvg('ratings as average_rating', 'rating')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $hotel
        ]);
    }

    public function show($id)
    {
        $hotel = Hotel::with('images')
            ->withAvg('ratings as average_rating', 'rating')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $hotel
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
}