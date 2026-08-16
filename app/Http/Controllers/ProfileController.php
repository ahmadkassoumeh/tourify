<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Utilities\ApiResponseService;
use App\Http\Resources\UserResource;

class ProfileController extends Controller
{
    /**
     * Get authenticated user's profile
     */
    public function show(Request $request)
    {
        $user = $request->user();

        return ApiResponseService::successResponse(
            data: [
                'user' => new UserResource($user),
            ],
            operation: 'get profile'
        );
    }


    /**
     * Update authenticated user's profile
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'username' => 'sometimes|string|max:50|unique:users,username,' . $user->id,
            'first_name' => 'sometimes|string|max:100',
            'last_name' => 'sometimes|string|max:100',
            'email' => 'sometimes|nullable|email|unique:users,email,' . $user->id,
            'phone_number' => 'sometimes|nullable|string|max:20|unique:users,phone_number,' . $user->id,
            'date_of_birth' => 'sometimes|nullable|date',
            'profile_image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return ApiResponseService::validateResponse(
                $validator->errors()
            );
        }

        $data = $request->only([
            'username',
            'first_name',
            'last_name',
            'email',
            'phone_number',
            'date_of_birth',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Profile Image
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('profile_image')) {

            // Delete old image
            if ($user->profile_image) {
                Storage::disk('users')->delete($user->profile_image);
            }

            $profilePath = $request->file('profile_image')->store(
                "{$user->id}/profile",
                'users'
            );

            $data['profile_image'] = $profilePath;
        }

        $user->update($data);

        $user->refresh();

        return ApiResponseService::successResponse(
            data: [
                'user' => new UserResource($user),
            ],
            operation: 'update profile'
        );
    }
}