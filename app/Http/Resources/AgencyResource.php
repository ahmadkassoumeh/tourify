<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgencyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'name' => $this->name,

            'description' => $this->description,

            'credit' => $this->credit,

            'landline_phone' => $this->landline_phone,

            'address' => $this->address,

            'images' => $this->images->map(function ($image) {

                return [

                    'url' => asset(
                        'storage/agency/' . $image->path
                    ),

                    'is_main' => $image->is_main,

                ];
            }),

        ];
    }
}
