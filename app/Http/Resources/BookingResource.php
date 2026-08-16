<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'package_id' => $this->bookable_id,

            'status' => $this->status,

            'booking_date' => $this->booking_date,

            'customer' => [

                'id' => $this->user->id,

                'name' => $this->user->first_name
                    . ' '
                    . $this->user->last_name,

                'phone' => $this->user->phone_number,

            ],

            'created_at' => $this->created_at?->toDateTimeString(),

        ];
    }
}