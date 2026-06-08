<?php

namespace App\Console\Commands;

use App\Enums\NotifyEventType;
use App\Events\NotificationEvent;
use App\Events\ScheduleLogEvent;
use App\Models\Admin;
use App\Models\TaskSchedule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RunTaskSchedule extends Command
{
    protected $signature = 'run-task-schedule';

    protected $description = 'Executes scheduled tasks based on their next_run time';

    public function handle()
    {
        $startTime = now();

        try {
            $this->processActiveSchedules($startTime);
        } catch (\Throwable $exception) {
            $this->handleFailure($startTime, $exception);

            return 1; // Return error code
        }

        return 0; // Success code
    }

    private function processActiveSchedules($startTime)
    {
        $schedules = TaskSchedule::query()
            ->where('status', 'active')
            ->where('next_run', '<=', $startTime)
            ->get();

        if ($schedules->isEmpty()) {
            return;
        }

        $this->executeAndUpdateSchedules($schedules, $startTime);
    }

    private function executeAndUpdateSchedules($schedules, $startTime)
    {
        $schedules->each(function (TaskSchedule $schedule) use ($startTime) {
            try {
                Artisan::call($schedule->command);

                $this->updateScheduleAndLogSuccess($schedule, $startTime);
            } catch (\Throwable $exception) {
                $this->logFailure($schedule, $startTime, $exception->getMessage());
            }
        });
    }

    private function updateScheduleAndLogSuccess(TaskSchedule $schedule, $startTime)
    {
        $endTime = now();
        $nextRun = now()->addSeconds((int) $schedule->schedule_time->interval);

        $schedule->update([
            'next_run' => $nextRun,
            'last_run' => $endTime,
        ]);

        $this->dispatchLogEvent($schedule, $startTime, $endTime, null);
    }

    private function handleFailure($startTime, \Throwable $exception)
    {
        $this->getActiveSchedules($startTime)->each(function (TaskSchedule $schedule) use ($startTime, $exception) {
            $this->logFailure($schedule, $startTime, $exception->getMessage());
        });

        event(new NotificationEvent(
            Admin::first(),
            NotifyEventType::SCHEDULE,
            'Cron failed',
            'Cron failed, check logs'
        ));
    }

    private function logFailure(TaskSchedule $schedule, $startTime, string $errorMessage)
    {
        $endTime = now();
        $nextRun = now()->addSeconds((int) $schedule->schedule_time->interval);
        
        $schedule->update([
            'next_run' => $nextRun,
            'last_run' => $endTime,
        ]);

        $this->dispatchLogEvent($schedule, $startTime, $endTime, $errorMessage);
    }

    private function dispatchLogEvent(TaskSchedule $schedule, $startTime, $endTime, $data)
    {
        ScheduleLogEvent::dispatch($schedule, [
            'started_at' => $startTime,
            'ended_at' => $endTime,
            'duration' => $endTime->diffInSeconds($startTime),
            'data' => $data,
        ]);
    }

    private function getActiveSchedules($startTime)
    {
        return TaskSchedule::where('status', 'active')
            ->where('next_run', '<=', $startTime)
            ->get();
    }
}
