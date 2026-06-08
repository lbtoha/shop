<?php

namespace App\Broadcasting;

use App\Services\Sms\SmsInterface;

class SmsChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @return void
     *
     * @throws \Exception
     */
    public function send($notifiable, $notification)
    {
        if (! method_exists($notification, 'toSMS')) {
            throw new \Exception('Notification must have a toSMS method.');
        }

        $sms = app(SmsInterface::class);
        $data = $notification->toSMS($notifiable);

        try {
            /**
             * Extract phone number correctly from AnonymousNotifiable
             */
            if ($notifiable instanceof \Illuminate\Notifications\AnonymousNotifiable) {
                $phone = $notifiable->routes['App\Broadcasting\SmsChannel'] ?? null;
            } else {
                $phone = $notifiable->phone ?? null;
            }

            if (config('extra_service.system_config.phone_notification.is_enabled') && $phone) {
                $sms->send($phone, $data['message']);
            }
        } catch (\Throwable $th) {
        }
    }
}
