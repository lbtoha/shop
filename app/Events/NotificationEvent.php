<?php

namespace App\Events;

use App\Enums\NotifyEventType;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public $user,
        public NotifyEventType $type,
        public string $title,
        public string $message
    ) {}
}
