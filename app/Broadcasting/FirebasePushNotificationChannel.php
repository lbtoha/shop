<?php

namespace App\Broadcasting;

use App\Services\Firebase\FirebaseNotificationService;

class FirebasePushNotificationChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    public function send($notifiable, $notification)
    {
        if (! method_exists($notification, 'toPushNotification')) {
            throw new \Exception('Notification must have a toPushNotification method.');
        }

        $firebase_service = FirebaseNotificationService::getInstance();

        $data = $notification->toPushNotification($notifiable);

        try {
            /**
             * Send SMS to the given number.
             */
            if (config('extra_service.system_config.push_notification.is_enabled') && $notifiable['fcm_token']) {
                $firebase_service->sendNotification(
                    $data['title'],
                    $data['message'],
                    $notifiable['fcm_token'],
                    $data['data'] ?? [],
                    $data['image'] ?? null
                );
            }
        } catch (\Throwable $th) {
            // throw $th;
        }
    }
}
