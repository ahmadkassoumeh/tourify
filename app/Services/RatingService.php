<?php

namespace App\Services;

use App\Models\Rating;

class RatingService
{
    public function rate($model, int $userId, int $rating)
    {
        return Rating::updateOrCreate(
            [
                'user_id'       => $userId,
                'rateable_id'   => $model->id,
                'rateable_type' => get_class($model),
            ],
            [
                'rating' => $rating,
            ]
        );
    }
}