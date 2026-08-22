<?php

namespace App\Http\Controllers;

use App\Enums\UserStatusEnum;
use App\Models\Airline;
use App\Models\Booking;
use App\Models\City;
use App\Models\Country;
use App\Models\Flight;
use App\Models\Hotel;
use App\Models\Package;
use App\Models\Place;
use App\Models\PlaceImage;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\HotelRoom;
use App\Models\RestaurantImage;
use App\Models\HotelImage;


use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    /**
     * Dashboard / Reports
     */
    public function dashboard()
    {
        $stats = [
            'users' => User::role('user')->count(),

            'agencies' => User::role('agency')->count(),

            'pending_agencies' => User::role('agency')
                ->where('status', UserStatusEnum::PENDING)
                ->count(),

            'airlines' => Airline::count(),

            'hotels' => Hotel::count(),

            'restaurants' => Restaurant::count(),

            'places' => Place::count(),

            'packages' => Package::count(),

            'bookings' => Booking::count(),
        ];

        $bookingStats = [
            'pending' => Booking::where('status', 'pending')->count(),

            'confirmed' => Booking::where('status', 'confirmed')->count(),

            'rejected' => Booking::where('status', 'rejected')->count(),

            'cancelled' => Booking::where('status', 'cancelled')->count(),
        ];

        $countries = Country::withCount([
            'places',
            'hotels',
            'restaurants',
            'packages',
        ])->with('cities')->get();

        $latestPackages = Package::with([
            'agency',
            'country',
        ])
            ->latest()
            ->take(5)
            ->get();

        $latestAgencies = User::role('agency')
            ->with('agency')
            ->latest()
            ->take(5)
            ->get();

        $latestBookings = Booking::with('user')
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'bookingStats',
            'countries',
            'latestPackages',
            'latestAgencies',
            'latestBookings'
        ));
    }

    /**
     * Create Place Page
     */
    public function createPlace()
    {
        $countries = Country::with([
            'cities:id,country_id,name'
        ])
            ->orderBy('name')
            ->get();

        return view('admin.places.create', compact('countries'));
    }

    /**
     * Store Place
     */
    public function storePlace(Request $request)
    {
        $data = $request->validate([

            'country_id' => [
                'required',
                'exists:countries,id',
            ],

            'city_id' => [
                'required',
                'exists:cities,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'url' => [
                'nullable',
                'url',
                'max:1000',
            ],

            'description' => [
                'required',
                'string',
            ],

            'history' => [
                'nullable',
                'string',
            ],

            'images' => [
                'required',
                'array',
                'min:1',
                'max:10',
            ],

            'images.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],
        ]);

        $city = City::where('id', $data['city_id'])
            ->where('country_id', $data['country_id'])
            ->first();

        if (!$city) {
            return back()
                ->withErrors([
                    'city_id' => 'المدينة لا تتبع للدولة المختارة.'
                ])
                ->withInput();
        }

        $place = DB::transaction(function () use ($data) {

            $place = Place::create([
                'city_id' => $data['city_id'],
                'name' => $data['name'],
                'url' => $data['url'] ?? null,
                'description' => $data['description'],
                'history' => $data['history'] ?? null,
            ]);

            foreach ($data['images'] as $index => $image) {

                $fileName = Str::random(20) . '.' .
                    $image->getClientOriginalExtension();

                $storagePath = "{$place->id}/{$fileName}";

                Storage::disk('place')->put(
                    $storagePath,
                    file_get_contents($image->getRealPath())
                );

                PlaceImage::create([
                    'place_id' => $place->id,
                    'path' => $storagePath,
                    'is_main' => $index === 0,
                ]);
            }

            return $place;
        });

        return redirect()
            ->route('admin.places.create')
            ->with('success', 'تمت إضافة المكان السياحي بنجاح.');
    }

    /**
     * Create Airline Page
     */
    public function createAirline()
    {
        return view('admin.airlines.create');
    }

    /**
     * Store Airline + User
     */
    public function storeAirline(Request $request)
    {
        $data = $request->validate([

            'airline_name' => [
                'required',
                'string',
                'max:255',
            ],

            'username' => [
                'required',
                'string',
                'max:50',
                'unique:users,username',
            ],

            'first_name' => [
                'required',
                'string',
                'max:100',
            ],

            'last_name' => [
                'required',
                'string',
                'max:100',
            ],

            'email' => [
                'nullable',
                'email',
                'unique:users,email',
            ],

            'phone_number' => [
                'required',
                'string',
                'max:20',
                'unique:users,phone_number',
            ],

            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
            ],
        ]);

        DB::transaction(function () use ($data) {

            $user = User::create([

                'username' => $data['username'],

                'first_name' => $data['first_name'],

                'last_name' => $data['last_name'],

                'email' => $data['email'] ?? null,

                'phone_number' => $data['phone_number'],

                'password' => Hash::make($data['password']),

                'status' => UserStatusEnum::APPROVED,

                'credit' => 0,
            ]);

            $user->assignRole('airline');

            Airline::create([

                'user_id' => $user->id,

                'name' => $data['airline_name'],

                'credit' => 0,
            ]);
        });

        return redirect()
            ->route('admin.airlines.create')
            ->with('success', 'تمت إضافة شركة الطيران بنجاح.');
    }

    public function createRestaurant()
    {
        $countries = Country::with('cities')
            ->orderBy('name')
            ->get();

        return view('admin.restaurants.create', compact('countries'));
    }

    public function storeRestaurant(Request $request)
    {
        $validated = $request->validate([
            'city_id' => [
                'required',
                'exists:cities,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
            ],

            'phone' => [
                'required',
                'string',
                'max:50',
            ],

            'images' => [
                'required',
                'array',
                'max:10',
            ],

            'images.*' => [
                'required',
                'image',
                'mimes:jpeg,png,webp',
                'max:5120',
            ],
        ]);

        DB::transaction(function () use ($validated, $request) {

            $restaurant = Restaurant::create([
                'city_id' => $validated['city_id'],
                'name' => $validated['name'],
                'description' => $validated['description'],
                'phone' => $validated['phone'],
            ]);

            foreach ($request->file('images') as $index => $image) {

                $path = $image->store(
                    $restaurant->id,
                    'restaurant'
                );

                RestaurantImage::create([
                    'restaurant_id' => $restaurant->id,
                    'path' => $path,
                    'is_main' => $index === 0,
                ]);
            }
        });

        return redirect()
            ->route('admin.restaurants.create')
            ->with('success', 'تمت إضافة المطعم وصوره بنجاح.');
    }

    public function createHotel()
    {
        $countries = Country::with('cities')
            ->orderBy('name')
            ->get();

        return view(
            'admin.hotels.create',
            compact('countries')
        );
    }

    public function storeHotel(Request $request)
    {
        $validated = $request->validate([

            'country_id' => [
                'required',
                'exists:countries,id',
            ],

            'city_id' => [
                'required',
                'exists:cities,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
            ],

            'phone' => [
                'required',
                'string',
                'max:50',
            ],

            'images' => [
                'required',
                'array',
                'min:1',
                'max:10',
            ],

            'images.*' => [
                'required',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:5120',
            ],

            /*
        |--------------------------------------------------------------------------
        | Room Types
        |--------------------------------------------------------------------------
        */

            'rooms.A.quantity' => [
                'required',
                'integer',
                'min:0',
            ],

            'rooms.A.price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'rooms.B.quantity' => [
                'required',
                'integer',
                'min:0',
            ],

            'rooms.B.price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'rooms.C.quantity' => [
                'required',
                'integer',
                'min:0',
            ],

            'rooms.C.price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'rooms.D.quantity' => [
                'required',
                'integer',
                'min:0',
            ],

            'rooms.D.price' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        /*
    |--------------------------------------------------------------------------
    | تأكد أن المدينة تتبع للدولة
    |--------------------------------------------------------------------------
    */

        $city = City::where('id', $validated['city_id'])
            ->where('country_id', $validated['country_id'])
            ->first();

        if (!$city) {

            return back()
                ->withErrors([
                    'city_id' => 'المدينة لا تتبع للدولة المختارة.'
                ])
                ->withInput();
        }


        DB::transaction(function () use ($validated, $request) {

            /*
        |--------------------------------------------------------------------------
        | Create Hotel
        |--------------------------------------------------------------------------
        */

            $hotel = Hotel::create([

                'city_id' => $validated['city_id'],

                'name' => $validated['name'],

                'description' => $validated['description'],

                'phone' => $validated['phone'],

                'credit' => 0,
            ]);


            /*
        |--------------------------------------------------------------------------
        | Store Hotel Images
        |--------------------------------------------------------------------------
        */

            foreach ($request->file('images') as $index => $image) {

                $fileName = \Illuminate\Support\Str::random(20)
                    . '.'
                    . $image->getClientOriginalExtension();

                $storagePath = "{$hotel->id}/{$fileName}";

                \Illuminate\Support\Facades\Storage::disk('hotel')->put(
                    $storagePath,
                    file_get_contents($image->getRealPath())
                );

                HotelImage::create([

                    'hotel_id' => $hotel->id,

                    'path' => $storagePath,

                    'is_main' => $index === 0,

                ]);
            }


            /*
        |--------------------------------------------------------------------------
        | Create Hotel Rooms
        |--------------------------------------------------------------------------
        */

            $roomTypes = [

                'A' => [
                    'capacity' => 4,
                ],

                'B' => [
                    'capacity' => 3,
                ],

                'C' => [
                    'capacity' => 2,
                ],

                'D' => [
                    'capacity' => 1,
                ],

            ];


            foreach ($roomTypes as $type => $roomData) {

                $quantity = $validated['rooms'][$type]['quantity'];

                $price = $validated['rooms'][$type]['price'];


                for ($i = 0; $i < $quantity; $i++) {

                    HotelRoom::create([

                        'hotel_id' => $hotel->id,

                        'type' => $type,

                        'capacity' => $roomData['capacity'],

                        'price' => $price,

                    ]);
                }
            }
        });


        return redirect()
            ->route('admin.hotels.create')
            ->with(
                'success',
                'تمت إضافة الفندق والغرف والصور بنجاح.'
            );
    }

    public function createCredit()
    {
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        })
            ->orderBy('first_name')
            ->get();

        return view(
            'admin.wallet.create',
            compact('users')
        );
    }

    public function addCredit(Request $request)
    {
        $validated = $request->validate([

            'user_id' => [
                'required',
                'exists:users,id',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:1',
            ],
        ]);


        $user = User::where('id', $validated['user_id'])
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'admin');
            })
            ->firstOrFail();


        $user->increment(
            'credit',
            $validated['amount']
        );


        return redirect()
            ->route('admin.wallet.create')
            ->with(
                'success',
                "تمت إضافة {$validated['amount']} إلى محفظة {$user->first_name}."
            );
    }
}
