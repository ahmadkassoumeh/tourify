<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\Agency;
use App\Models\AgencyImage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Enums\UserStatusEnum;
use App\Utilities\ApiResponseService;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|max:20|unique:users,phone_number',
            'email' => 'nullable|email|unique:users,email',
            'username' => 'required|string|max:50|unique:users,username',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'date_of_birth' => 'nullable|date',
            'password' => 'required|min:6|confirmed',

            'role' => 'required|in:user,agency',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'id_card_image' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',

            'agency_name' => 'required_if:role,agency|string|max:255',

            'agency_description' => 'nullable|string',

            'agency_landline_phone' => 'required_if:role,agency|string|max:30',

            'agency_address' => 'required_if:role,agency|string|max:255',

            'agency_image' => 'required_if:role,agency|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        if ($validator->fails()) {
            return ApiResponseService::validateResponse(
                $validator->errors()
            );
        }

        $status = $request->role === 'agency'
            ? UserStatusEnum::PENDING
            : UserStatusEnum::APPROVED;

        $user = User::create([
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'username' => $request->username,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'date_of_birth' => $request->date_of_birth,
            'password' => Hash::make($request->password),
            'status' => $status
        ]);

        if ($request->role === 'agency') {

            $agency = Agency::create([

                'user_id' => $user->id,

                'name' => $request->agency_name,

                'landline_phone' => $request->agency_landline_phone,

                'address' => $request->agency_address,

                'description' => $request->agency_description,

            ]);
        }

        if ($request->hasFile('agency_image')) {

            $image = $request->file('agency_image');

            $fileName = Str::random(20) . '.'
                . $image->getClientOriginalExtension();

            $storagePath = "{$agency->id}/{$fileName}";

            Storage::disk('agency')->put(
                $storagePath,
                file_get_contents($image->getRealPath())
            );

            AgencyImage::create([

                'agency_id' => $agency->id,

                'path' => $storagePath,

                'is_main' => true,

            ]);
        }

        if ($request->hasFile('profile_image')) {
            $profilePath = $request->file('profile_image')->store(
                "{$user->id}/profile",
                'users'
            );

            $user->update([
                'profile_image' => $profilePath
            ]);
        }

        if ($request->hasFile('id_card_image')) {
            $idCardPath = $request->file('id_card_image')->store(
                "{$user->id}/id-card",
                'users'
            );

            $user->update([
                'id_card_image' => $idCardPath
            ]);
        }


        $user->assignRole($request->role);

        $token = $user->createToken('API Token')->accessToken;



        return ApiResponseService::createdResponse(
            data: [
                'user' => new UserResource($user),
                'token' => $token,
            ]
        );
    }



    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return ApiResponseService::validateResponse(
                $validator->errors()
            );
        }

        $user = User::where('username', $request->username)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return ApiResponseService::unauthorizedResponse(
                msg: 'بيانات الدخول غير صحيحة'
            );
        }

        if ($user->status === UserStatusEnum::REJECTED->value) {
            return ApiResponseService::unauthorizedResponse(
                msg: 'الحساب قد تم رفضه من قبل الادارة'
            );
        }

        // 👈 هون المهم
        if ($user->status !== UserStatusEnum::APPROVED->value) {
            return ApiResponseService::unauthorizedResponse(
                msg: 'الحساب بانتظار موافقة الإدارة'
            );
        }

        $token = $user->createToken('API Token')->accessToken;
        $userRole = $user->getRoleNames()->first();

        return ApiResponseService::successResponse(
            data: [
                'user' => $user,
                'role' => $userRole,
                'token' => $token,
            ],
            operation: 'login'
        );
    }


    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'تم تسجيل الخروج بنجاح'
        ]);
    }

    // AuthController.php
    public function status(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'User is authenticated and token is valid',
            'data' => [
                'user' => [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'first_name' => $request->user()->first_name,  // إذا أضفتها
                    'last_name' => $request->user()->last_name,    // إذا أضفتها
                ],
                'token_valid' => true,
                'authenticated_at' => now()->toDateTimeString(),
                'token_expires_at' => $this->getTokenExpiration($request), // اختياري
            ]
        ], 200);
    }

    // دالة اختيارية لحساب انتهاء صلاحية التوكن
    private function getTokenExpiration(Request $request)
    {
        if (method_exists($request->user()->currentAccessToken(), 'expires_at')) {
            return $request->user()->currentAccessToken()->expires_at;
        }

        // إذا كنت تستخدم Laravel Sanctum
        if (config('passport.expiration')) {
            return now()->addMinutes(config('passport.expiration'));
        }

        return null;
    }
}
