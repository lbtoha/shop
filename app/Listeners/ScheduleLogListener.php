<?php

namespace App\Listeners;

use App\Events\ScheduleLogEvent;

class ScheduleLogListener
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
    public function handle(ScheduleLogEvent $event): void
    {
        $taskSchedule = $event->taskSchedule;
        $data = $event->data;
        $taskSchedule->schedule_logs()->create([
            'title' => $taskSchedule->name,
            'started_at' => $data['started_at'] ?? null,
            'ended_at' => $data['ended_at'] ?? null,
            'duration' => $data['duration'] ?? null,
            'data' => $data['data'] ?? null,
        ]);
    }
}
