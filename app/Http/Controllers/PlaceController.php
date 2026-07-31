<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Services\RatingService;
use App\Services\FavoriteService;
use Illuminate\Validation\ValidationException;
use App\Models\City;
use App\Http\Resources\PlaceResource;
use App\Utilities\ApiResponseService;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    public function __construct(
        protected RatingService $ratingService,
        protected FavoriteService $favoriteService
    ) {}

    public function index()
    {
        $places = Place::with(['images' => function ($query) {
            $query->where('is_main', true);
        }])
            ->withAvg('ratings as average_rating', 'rating')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $places
        ]);
    }

    public function show($id)
    {
        $place = Place::with('images')
            ->withAvg('ratings as average_rating', 'rating')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $place
        ]);
    }

    public function rate(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $place = Place::findOrFail($id);

        $this->ratingService->rate(
            $place,
            auth()->id(),
            $request->rating
        );

        return response()->json([
            'message' => 'Rating saved.'
        ]);
    }

    public function toggleFavorite($id)
    {
        $place = Place::findOrFail($id);

        $favorite = $this->favoriteService->toggle(
            $place,
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

        if (! $city) {
            throw ValidationException::withMessages([
                'city_id' => 'The selected city does not belong to the selected country.',
            ]);
        }

        $places = Place::with([
            'city.country',
            'images',
        ])
            ->where('city_id', $city->id)
            ->orderBy('name')
            ->get();

        return ApiResponseService::successResponse(
            data: PlaceResource::collection($places)
        );
    }
}
