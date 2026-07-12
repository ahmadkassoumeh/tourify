<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Services\RatingService;
use App\Services\FavoriteService;
use App\Utilities\ApiResponseService;
use App\Models\City;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function __construct(
        protected RatingService $ratingService,
        protected FavoriteService $favoriteService
    ) {}

    public function index()
    {
        $restaurants = Restaurant::with(['images' => function ($query) {
            $query->where('is_main', true);
        }])
            ->withAvg('ratings as average_rating', 'rating')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $restaurants
        ]);
    }

    public function show($id)
    {
        $restaurant = Restaurant::with('images')
            ->withAvg('ratings as average_rating', 'rating')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $restaurant
        ]);
    }

    public function rate(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $restaurant = Restaurant::findOrFail($id);

        $this->ratingService->rate(
            $restaurant,
            auth()->id(),
            $request->rating
        );

        return response()->json([
            'message' => 'Rating saved.'
        ]);
    }

    public function toggleFavorite($id)
    {
        $restaurant = Restaurant::findOrFail($id);

        $favorite = $this->favoriteService->toggle(
            $restaurant,
            auth()->id()
        );

        return response()->json([
            'is_favorite' => $favorite
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

        if (!$city) {
            throw ValidationException::withMessages([
                'city_id' => 'The selected city does not belong to the selected country.',
            ]);
        }

        $restaurants = Restaurant::where('city_id', $city->id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return ApiResponseService::successResponse(
            data: $restaurants
        );
    }

}
