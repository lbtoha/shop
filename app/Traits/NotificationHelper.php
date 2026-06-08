<?php

namespace App\Traits;

use Illuminate\Notifications\AnonymousNotifiable;

trait NotificationHelper
{
    /**
     * Check if notifiable has a non-empty property
     */
    protected function hasValue(object $notifiable, string $property): bool
    {
        return isset($notifiable->{$property}) && ! empty($notifiable->{$property});
    }

    /**
     * Check AnonymousNotifiable routing
     */
    protected function hasAnonymousRoute(object $notifiable, string $channel): bool
    {
        return $notifiable instanceof AnonymousNotifiable
            && ! empty($notifiable->routeNotificationFor($channel));
    }


    private function getModelOrRoute(object $notifiable, string $channel)
    {
        $channels = [
            'mail' => 'email',
            'sms' => 'phone',
            'push' => 'fcm_token',
        ];

        return $this->hasValue($notifiable, $channels[$channel])
            ? $notifiable
            : [
                $channels[$channel] => $this->hasAnonymousRoute($notifiable, $channel)
                    ? $notifiable->routeNotificationFor($channel)
                    : null,
            ];

    }
}
