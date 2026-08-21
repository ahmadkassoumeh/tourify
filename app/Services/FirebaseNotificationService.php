<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Http;

class FirebaseNotificationService
{
    public function send(
        string $fcmToken,
        string $title,
        string $body,
        array $data = []
    ): array {

        // اقرأ إعدادات Firebase من config وليس env مباشرة
        $credentialsPath = base_path(
            config(
                'services.firebase.credentials',
                'storage/app/firebase/firebase-service-account.json'
            )
        );

        if (!file_exists($credentialsPath)) {
            throw new \Exception(
                "Firebase credentials file not found: {$credentialsPath}"
            );
        }

        $credentials = new ServiceAccountCredentials(
            ['https://www.googleapis.com/auth/firebase.messaging'],
            $credentialsPath
        );

        $authTokenData = $credentials->fetchAuthToken();

        if (empty($authTokenData['access_token'])) {
            throw new \Exception(
                'Firebase authentication failed: access token not returned.'
            );
        }

        $authToken = $authTokenData['access_token'];

        // مهم: استخدم config وليس env
        $projectId = config('services.firebase.project_id');

        if (empty($projectId)) {
            throw new \Exception(
                'Firebase project ID is missing.'
            );
        }

        // Firebase FCM data يجب أن تكون map:
        // key => string value
        $firebaseData = [];

        foreach ($data as $key => $value) {
            $firebaseData[(string) $key] = (string) $value;
        }

        $message = [
            'token' => $fcmToken,

            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
        ];

        if (!empty($firebaseData)) {
            $message['data'] = $firebaseData;
        }

        $response = Http::withToken($authToken)
            ->post(
                "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send",
                [
                    'message' => $message,
                ]
            );

        if (!$response->successful()) {
            throw new \Exception(
                'Firebase notification failed: ' . $response->body()
            );
        }

        return $response->json();
    }
}