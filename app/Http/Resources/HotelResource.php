<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'name' => $this->name,

            'phone' => $this->phone,

            'description' => $this->description,

            'city' => new CityResource(
                $this->whenLoaded('city')
            ),

            'room_types' => $this->whenLoaded(
                'rooms',
                fn() => $this->rooms
                    ->pluck('type')
                    ->unique()
                    ->values()
            ),

            'images' => $this->images->map(function ($image) {

                return [

                    'url' => asset(
                        'storage/hotel/' . $image->path
                    ),

                    'is_main' => $image->is_main,

                ];
            }),

        ];
    }
}
