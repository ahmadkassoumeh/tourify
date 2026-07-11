<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\Restaurant;
use App\Models\Hotel;
use App\Models\Airline;
use App\Models\Agency;
use App\Models\Package;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Country;
use App\Models\Booking;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $country = $request->query('country');
        $country_id = Country::where('name', $country)->value('id');
        $city_id = City::where('country_id', $country_id)->value('id');

        $places = Place::with(['images' => function ($query) {
            $query->where('is_main', true);
            }])
            ->withAvg('ratings as average_rating', 'rating')
            ->when($city_id, function ($query) use ($city_id) {
                $query->where('city_id', $city_id);
            })
            ->latest()
            ->take(4)
            ->get();

        $restaurants = Restaurant::with(['images' => function ($query) {
            $query->where('is_main', true);
            }])
            ->withAvg('ratings as average_rating', 'rating')
            ->when($city_id, function ($query) use ($city_id) {
                $query->where('city_id', $city_id);
            })
            ->latest()
            ->take(4)
            ->get();

        $hotels = Hotel::with(['images' => function ($query) {
            $query->where('is_main', true);
            }])
            ->withAvg('ratings as average_rating', 'rating')
            ->when($city_id, function ($query) use ($city_id) {
                $query->where('city_id', $city_id);
            })
            ->latest()
            ->take(4)
            ->get();

        $airlines = Airline::withAvg('ratings as average_rating', 'rating')
            ->latest()
            ->take(4)
            ->get();

        $agencies = Agency::with(['images' => function ($query) 
            {
            $query->where('is_main', true);
            }])
            ->withAvg('ratings as average_rating', 'rating')
            ->latest()
            ->take(4)
            ->get();

        $packages = Package::latest()
            ->when($country_id, function ($query) use ($country_id) {
                $query->where('country_id', $country_id);
            })
            ->take(4)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'places' => $places,
                'restaurants' => $restaurants,
                'hotels' => $hotels,
                'airlines' => $airlines,
                'agencies' => $agencies,
                'packages' => $packages
            ]
        ]);
    }

    public function getfavorites(Request $request)
    {
        $user = $request->user();

        $favoritePlaces = $user->favorites()->where('favoriteable_type', Place::class)
        ->with(['favoriteable.images' => function ($query) {$query->where('is_main', true);}])
        ->get();

        $favoriteRestaurants = $user->favorites()->where('favoriteable_type', Restaurant::class)
        ->with(['favoriteable.images' => function ($query) {$query->where('is_main', true);}])
        ->get();

        $favoriteHotels = $user->favorites()->where('favoriteable_type', Hotel::class)
        ->with(['favoriteable.images' => function ($query) {$query->where('is_main', true);}])
        ->get();

        $favoriteAirlines = $user->favorites()->where('favoriteable_type', Airline::class)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'places' => $favoritePlaces,
                'restaurants' => $favoriteRestaurants,
                'hotels' => $favoriteHotels,
                'airlines' => $favoriteAirlines
            ]
        ]);
    }

    public function getbookings(Request $request)
    {
        $user = $request->user();

        $bookings = Booking::where('user_id', $user->id)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'bookings' => $bookings,
            ]
        ]);
    }
}