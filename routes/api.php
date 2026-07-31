<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\AirlineController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\DashboardController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);


Route::middleware('auth:api')->group(function () {

    Route::post('/packages', [PackageController::class, 'store']);

    Route::get('/places-drop-list', [PlaceController::class, 'dropList']);
    Route::get('/hotels-drop-list', [HotelController::class, 'dropList']);
    Route::get('/restaurants-drop-list', [RestaurantController::class, 'dropList']);
    Route::get('/airlines-drop-list', [AirlineController::class, 'dropList']);

    Route::post('/package-hint', [PackageController::class, 'hint']);

});









// هيثم
$sample = 1;

Route::get('/dashboard', [DashboardController::class, 'index']);

Route::get('/places', [PlaceController::class, 'index']);
Route::get('/places/{id}', [PlaceController::class, 'show']);

Route::get('/restaurants', [RestaurantController::class, 'index']);
Route::get('/restaurants/{id}', [RestaurantController::class, 'show']);

Route::get('/airlines', [AirlineController::class, 'index']);
Route::get('/airlines/{id}/flights', [AirlineController::class, 'flights']);
Route::get('/flights/{id}/schedules', [AirlineController::class, 'schedules']);

Route::get('/hotels', [HotelController::class, 'index']);
Route::get('/hotels/{id}', [HotelController::class, 'show']);

Route::middleware('auth:api')->group(function () {
    Route::get('/dashboard/favorites', [DashboardController::class, 'getfavorites']);
    Route::get('/dashboard/bookings', [DashboardController::class, 'getbookings']);

    Route::post('/places/{id}/rate', [PlaceController::class, 'rate']);
    Route::post('/places/{id}/favorite', [PlaceController::class, 'toggleFavorite']);
    
    Route::post('/restaurants/{id}/rate', [RestaurantController::class, 'rate']);
    Route::post('/restaurants/{id}/favorite', [RestaurantController::class, 'toggleFavorite']);
    
    Route::post('/airlines/{id}/rate', [AirlineController::class, 'rate']);
    Route::post('/airlines/{id}/favorite', [AirlineController::class, 'toggleFavorite']);
    Route::post('/flights/{id}/book', [AirlineController::class, 'bookFlight']);

    Route::post('/hotels/{id}/rate', [HotelController::class, 'rate']);
    Route::post('/hotels/{id}/favorite', [HotelController::class, 'toggleFavorite']);
    Route::post('/rooms/{id}/book', [HotelController::class, 'bookRoom']);
});