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
use Spatie\Permission\Middleware\RoleMiddleware;
use App\Http\Controllers\BookPackageController;
use App\Http\Controllers\FlightScheduleController;
use App\Http\Controllers\AgencyController;
use App\Http\Controllers\ProfileController;
use App\Services\FirebaseNotificationService;

Route::middleware('auth:api')->post('/test-notification', function (Request $request, FirebaseNotificationService $firebase) {

    $request->validate([
        'title' => 'required|string',
        'body' => 'required|string',
    ]);

    $user = $request->user();

    if (!$user->fcm_token) {
        return response()->json([
            'message' => 'User does not have an FCM token.'
        ], 422);
    }

    $firebase->send(
        $user->fcm_token,
        $request->title,
        $request->body
    );

    return response()->json([
        'message' => 'Notification sent successfully.'
    ]);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);


Route::middleware('auth:api')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);


    //! Route for create package case : 

    Route::post('/packages', [PackageController::class, 'store'])->middleware(RoleMiddleware::class . ':agency');
    Route::get('/places-drop-list', [PlaceController::class, 'dropList'])->middleware(RoleMiddleware::class . ':agency');
    Route::get('/hotels-drop-list', [HotelController::class, 'dropList'])->middleware(RoleMiddleware::class . ':agency');
    Route::get('/restaurants-drop-list', [RestaurantController::class, 'dropList'])->middleware(RoleMiddleware::class . ':agency');
    Route::get('/airlines-drop-list', [AirlineController::class, 'dropList'])->middleware(RoleMiddleware::class . ':agency');
    Route::post('/package-hint', [PackageController::class, 'hint'])->middleware(RoleMiddleware::class . ':agency');
    //!

    Route::get('/country', [PackageController::class, 'country']);

    //^ زبون يحجز
    Route::post(
        '/packages/book',
        [BookPackageController::class, 'store']
    )->middleware(RoleMiddleware::class . ':user');

    Route::get(
        '/agency/packages/active',
        [BookPackageController::class, 'activePackages']
    );

    Route::get(
        '/agencies/packages/{package}/bookings/pending',
        [BookPackageController::class, 'pendingBookings']
    );

    Route::post(
        '/agencies/packages/{package}/bookings/{booking}/approve',
        [BookPackageController::class, 'approve']
    );

    Route::post(
        '/agencies/packages/{package}/bookings/{booking}/reject',
        [BookPackageController::class, 'reject']
    );

    Route::delete(
        '/packages/{package}/bookings/{booking}/cancel',
        [BookPackageController::class, 'cancel']
    );

    Route::get('/all-packages', [PackageController::class, 'allPackages']);

    Route::get('/package/{id}/details', [PackageController::class, 'getPackageById']);

    Route::post(
        '/flight-schedules',
        [FlightScheduleController::class, 'store']
    )->middleware(RoleMiddleware::class . ':airline');

    Route::get('/flights/dropdown', [FlightScheduleController::class, 'getFlightsDropdown'])
        ->middleware(RoleMiddleware::class . ':airline');



});







// هيثم
$sample = 1;

Route::middleware('auth:api')->group(function () {
    Route::post('/fcm-token', function (Request $request) {

        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $request->user()->update([
            'fcm_token' => $request->fcm_token,
        ]);

        return response()->json([
            'message' => 'FCM token saved successfully',
        ]);
    });
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);

    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/search', [DashboardController::class, 'search']);
    Route::get('/dashboard/favorites', [DashboardController::class, 'getfavorites']);
    Route::get('/dashboard/bookings', [DashboardController::class, 'getbookings']);

    Route::get('/places', [PlaceController::class, 'index']);
    Route::get('/places/{id}', [PlaceController::class, 'show']);
    Route::post('/places/{id}/rate', [PlaceController::class, 'rate']);
    Route::post('/places/{id}/favorite', [PlaceController::class, 'toggleFavorite']);

    Route::get('/restaurants', [RestaurantController::class, 'index']);
    Route::get('/restaurants/{id}', [RestaurantController::class, 'show']);
    Route::post('/restaurants/{id}/rate', [RestaurantController::class, 'rate']);
    Route::post('/restaurants/{id}/favorite', [RestaurantController::class, 'toggleFavorite']);

    Route::get('/airlines', [AirlineController::class, 'index']);
    Route::get('/airlines/{id}/flights', [AirlineController::class, 'flights']);
    Route::get('/flights/{id}/schedules', [AirlineController::class, 'schedules']);
    Route::post('/airlines/{id}/rate', [AirlineController::class, 'rate']);
    Route::post('/airlines/{id}/favorite', [AirlineController::class, 'toggleFavorite']);
    Route::post('/flights/{id}/book', [AirlineController::class, 'bookFlight']);

    Route::get('/hotels', [HotelController::class, 'index']);
    Route::get('/hotels/{id}', [HotelController::class, 'show']);
    Route::post('/hotels/{id}/rate', [HotelController::class, 'rate']);
    Route::post('/hotels/{id}/favorite', [HotelController::class, 'toggleFavorite']);
    Route::post('/rooms/{id}/book', [HotelController::class, 'bookRoom']);

    Route::get('/agencies', [AgencyController::class, 'index']);
    Route::get('/agencies/{id}', [AgencyController::class, 'show']);
    Route::post('/agencies/{id}/rate', [AgencyController::class, 'rate']);
    Route::post('/agencies/{id}/favorite', [AgencyController::class, 'toggleFavorite']);
});