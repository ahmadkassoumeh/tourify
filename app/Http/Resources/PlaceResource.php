<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlaceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'name' => $this->name,

            'description' => $this->description,

            'city' => new CityResource(
                $this->whenLoaded('city')
            ),

            'images' => $this->images->map(function ($image) {

                return [

                    'url' => asset(
                        'storage/place/' . $image->path
                    ),

                    'is_main' => $image->is_main,

                ];
            }),

        ];
    }
}
