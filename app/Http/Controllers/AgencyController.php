<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Services\RatingService;
use App\Services\FavoriteService;
use App\Utilities\ApiResponseService;
use Illuminate\Http\Request;

class AgencyController extends Controller
{
    public function __construct(
        protected RatingService $ratingService,
        protected FavoriteService $favoriteService
    ) {}

    public function index()
    {
        $userId = auth()->id();

        $agencies = Agency::with(['images'])
            ->withAvg('ratings as average_rating', 'rating')
            ->get()
            ->map(function ($agency) use ($userId) {
                $agency->is_favorite = $agency->favorites()
                    ->where('user_id', $userId)
                    ->exists();

                return $agency;
            });

        return response()->json([
            'success' => true,
            'data' => $agencies
        ]);
    }

    public function show($id)
    {
        $userId = auth()->id();

        $agency = Agency::with(['images', 'packages'])
            ->withAvg('ratings as average_rating', 'rating')
            ->withExists([
                'favorites as is_favorite' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }
            ])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $agency
        ]);
    }

    public function rate(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $agency = Agency::findOrFail($id);

        $this->ratingService->rate(
            $agency,
            auth()->id(),
            $request->rating
        );

        return response()->json([
            'message' => 'Rating saved.'
        ]);
    }

    public function toggleFavorite($id)
    {
        $agency = Agency::findOrFail($id);

        $favorite = $this->favoriteService->toggle(
            $agency,
            auth()->id()
        );

        return response()->json([
            'is_favorite' => $favorite
        ]);
    }
}