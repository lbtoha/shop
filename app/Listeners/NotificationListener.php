<?php

namespace App\Listeners;

use App\Events\NotificationEvent;

class NotificationListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NotificationEvent $event): void
    {

        $user = $event->user;

        $type = $event->type;

        $title = $event->title;

        $message = $event->message;

        $user->notifications()->create([
            'type' => $type,
            'data' => $message,
        ]);
    }
}
