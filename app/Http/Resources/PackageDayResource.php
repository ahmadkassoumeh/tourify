<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Models\Place;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\FlightSchedule;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageDayResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $place = $this->items
            ->first(
                fn($i) => $i->itemable instanceof Place
            );

        $hotel = $this->items
            ->first(
                fn($i) => $i->itemable instanceof Hotel
            );
        

        $restaurant = $this->items
            ->first(
                fn($i) => $i->itemable instanceof Restaurant
            );

        $flight = $this->items
            ->first(
                fn($i) => $i->itemable instanceof FlightSchedule
            );

        return [

            // 'day' => $this->day_number,

            'date' => $this->date,

            // 'room_type' => $this->room_type,

            'place' => $place
                ? new PlaceResource($place->itemable)
                : null,

            'hotel' => $hotel
                ? new HotelResource($hotel->itemable)
                : null,

            'restaurant' => $restaurant
                ? new RestaurantResource($restaurant->itemable)
                : null,

            'flight' => $flight ? [
                'id' => $flight->itemable->id,
                'departure' => $flight->itemable->departure_time,
                'arrival' => $flight->itemable->arrival_time,
            ] : null,

        ];

    }
}
