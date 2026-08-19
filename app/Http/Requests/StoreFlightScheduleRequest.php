<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFlightScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'flight_id' => [
                'required',
                'exists:flights,id',
            ],

            'departure_time' => [
                'required',
                'date_format:H:i',
            ],

            'arrival_time' => [
                'required',
                'date_format:H:i',
                'different:departure_time',
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'weeks' => [
                'required',
                'integer',
                'min:1',
                'max:52',
            ],

            'days_of_week' => [
                'required',
                'array',
                'min:1',
                'max:7',
            ],

            'days_of_week.*' => [
                'required',
                'integer',
                'between:0,6',
                'distinct',
            ],
        ];
    }
}