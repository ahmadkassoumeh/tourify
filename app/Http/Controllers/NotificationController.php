<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{
    public function test(Request $request)
    {
        $user = $request->user();

        if (!$user->fcm_token) {
            return response()->json([
                'message' => 'User does not have FCM token'
            ], 400);
        }

        // مؤقتاً للاختبار
        return response()->json([
            'message' => 'FCM token exists',
            'token' => $user->fcm_token,
        ]);
    }
}