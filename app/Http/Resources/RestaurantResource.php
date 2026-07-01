<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
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

            'images' => $this->images->map(function ($image) {

                return [

                    'url' => asset(
                        'storage/restaurant/' . $image->path
                    ),

                    'is_main' => $image->is_main,

                ];
            }),

        ];
    }
}
