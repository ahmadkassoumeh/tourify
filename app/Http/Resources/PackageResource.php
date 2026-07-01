<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'name' => $this->name,

            'description' => $this->description,

            'price' => $this->price,

            'quantity' => $this->quantity,

            'number_of_days' => $this->number_of_days,

            'agency' => new AgencyResource(
                $this->whenLoaded('agency')
            ),

            'country' => new CountryResource(
                $this->whenLoaded('country')
            ),

            'days' => PackageDayResource::collection(
                $this->whenLoaded('days')
            ),

        ];
    }
}
