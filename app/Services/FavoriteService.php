<?php

namespace App\Services;

use App\Models\Favorite;

class FavoriteService
{
    public function toggle($model, int $userId): bool
    {
        $favorite = Favorite::where([
            'user_id' => $userId,
            'favoriteable_id' => $model->id,
            'favoriteable_type' => get_class($model),
        ])->first();

        if ($favorite) {
            $favorite->delete();
            return false;
        }

        Favorite::create([
            'user_id' => $userId,
            'favoriteable_id' => $model->id,
            'favoriteable_type' => get_class($model),
        ]);

        return true;
    }
}