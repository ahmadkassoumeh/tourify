<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\City;
use App\Models\Hotel;
use App\Models\Place;
use App\Models\Restaurant;
use Illuminate\Foundation\Http\FormRequest;

class StorePackageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

            'name' => 'required|string|max:255',

            'country_id' => 'required|exists:countries,id',

            'quantity' => 'required|integer|min:1',

            'price' => 'required|numeric|min:1',

            'description' => 'required|string|max:1000',

            'days.*.date' => 'required|date',

            'days.*.flight_schedule_id' => 'nullable|exists:flight_schedules,id',

            'days' => 'required|array',

            'days.*.place_id' => 'required|exists:places,id',

            'days.*.hotel_id' => 'required|exists:hotels,id',

            'days.*.restaurant_id' => 'required|exists:restaurants,id',

            'days.*.room_type' => 'required|string',

        ];
    }

    // public function withValidator($validator): void
    // {
    //     $validator->after(function ($validator) {

    //         $countryId = $this->country_id;

    //         foreach ($this->days as $index => $day) {

    //             $hotel = Hotel::find($day['hotel_id']);
    //             $restaurant = Restaurant::find($day['restaurant_id']);
    //             $place = Place::find($day['place_id']);

    //             // جميع العناصر يجب أن تنتمي لنفس المدينة
    //             if (
    //                 $hotel->city_id != $restaurant->city_id ||
    //                 $hotel->city_id != $place->city_id
    //             ) {
    //                 $validator->errors()->add(
    //                     "days.$index",
    //                     "Hotel, restaurant and place must belong to the same city."
    //                 );

    //                 continue;
    //             }

    //             // المدينة يجب أن تنتمي للدولة المختارة
    //             $city = City::find($hotel->city_id);

    //             if ($city->country_id != $countryId) {

    //                 $validator->errors()->add(
    //                     "days.$index",
    //                     "Selected data does not belong to the selected country."
    //                 );
    //             }
    //         }
    //     });
    // }

}
