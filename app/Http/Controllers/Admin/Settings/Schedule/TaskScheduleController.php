<?php

namespace App\Http\Controllers\Admin\Settings\Schedule;

use App\Enums\NotifyEventType;
use App\Events\NotificationEvent;
use App\Events\ScheduleLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TaskScheduleRequest;
use App\Models\ScheduleTime;
use App\Models\TaskSchedule;
use App\Services\ModalIndexQuey;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class TaskScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        adminUserHasPermission('read');

        $buttons = [
            [
                'label' => __('Add New Cron Job'),
                'icon' => 'ph ph-plus-bold',
                'type' => 'modal',
                'id' => 'task_schedule_create_modal',
                'href' => route('admin.settings.task-schedules.store'),
            ],
            [
                'label' => __('Crons'),
                'icon' => 'ph ph-list',
                'type' => 'link',
                'link' => route('admin.settings.schedule-times.index'),
            ],
        ];

        $task_schedules = ModalIndexQuey::get(model: TaskSchedule::query());

        $columns = [
            [
                'label' => __('Name'),
                'key' => 'name',
                'is_sortable' => true,
            ],
            [
                'label' => __('Last Run'),
                'key' => 'last_run',
                'render' => function ($task) {
                    return $task->last_run
                        ? Carbon::parse($task->last_run)->diffForHumans()
                        : '-';
                },
            ],

            [
                'label' => __('Next Run'),
                'key' => 'next_run',
                'render' => function ($task) {

                    $next_run_date = $task->next_run ? Carbon::parse($task->next_run) : null;

                    if (! $next_run_date) {
                        return '-';
                    }

                    // ----- MISSED -----
                    if ($next_run_date->isPast()) {

                        $diff = $next_run_date->diffForHumans([
                            'parts' => 2,
                            'short' => false,
                            'syntax' => CarbonInterface::DIFF_RELATIVE_TO_NOW,
                        ]);

                        return '<span class="text-red-600 font-bold">Missed '.$diff.'</span>';
                    }

                    // ----- TODAY -----
                    if ($next_run_date->isToday()) {

                        $diff = $next_run_date->diffForHumans([
                            'parts' => 2,
                            'short' => false,
                            'syntax' => CarbonInterface::DIFF_RELATIVE_TO_NOW,
                        ]);

                        return '<span class="text-green-600 font-semibold">Starting '.$diff.'</span>';
                    }

                    // ----- TOMORROW -----
                    if ($next_run_date->isTomorrow()) {
                        return '<span class="text-green-600 font-semibold">Starting tomorrow at '.$next_run_date->format('g:i A').'</span>';
                    }

                    // ----- FUTURE DATE -----
                    return '<span class="text-green-600 font-semibold">Starting on '.$next_run_date->format('d M, g:i A').'</span>';
                },
            ],

            [
                'label' => __('Status'),
                'key' => 'status',
                'is_sortable' => true,
                'render' => function ($language) {
                    $color = $language->status == 'active' ? 'success' : 'danger';

                    return '<span class="status '.$color.' capitalize">'.__($language->status).'</span>';
                },
            ],
            [
                'label' => __('Action'),
                'render' => function ($task) {
                    $action_buttons = [
                        [
                            'label' => __('Edit'),
                            'icon' => 'ph ph-pencil',
                            'id' => 'task_schedule_edit_modal',
                            'row' => $task,
                            'type' => 'modal',
                            'href' => route('admin.settings.task-schedules.update', $task->id),
                        ],
                        [
                            'label' => $task->status == 'active' ? __('Deactivate') : __('Activate'),
                            'icon' => $task->status == 'active' ? 'ph ph-toggle-left' : 'ph ph-toggle-right',
                            'type' => 'link',
                            'href' => route('admin.settings.task-schedules.status-change', $task->id),
                        ],
                        [
                            'label' => __('Run Now'),
                            'icon' => 'ph ph-play',
                            'type' => 'link',
                            'href' => route('admin.settings.task-schedules.run', $task->id),
                        ],
                        [
                            'label' => __('Logs'),
                            'icon' => 'ph ph-list',
                            'type' => 'link',
                            'href' => route('admin.settings.task-schedules.logs', $task->id),
                        ],
                        [
                            'label' => __('Delete'),
                            'icon' => 'ph ph-trash',
                            'type' => 'delete',
                            'class' => 'form-submit-delete',
                            'href' => route('admin.settings.task-schedules.destroy', $task->id),
                        ],
                    ];

                    return view('admin.components.table-action', compact('action_buttons'))->render();
                },
            ],
        ];

        $task_times = ScheduleTime::select('name', 'id')->get();

        return view('admin.pages.settings.cron-schedules.index', compact('task_schedules', 'columns', 'buttons', 'task_times'));

    }

    public function store(TaskScheduleRequest $request)
    {
        adminUserHasPermission('create');

        TaskSchedule::create($request->validated());

        return response()->json(['message' => __('Cron created successfully'), 'reload' => true]);
    }

    public function update(TaskScheduleRequest $request, TaskSchedule $taskSchedule)
    {
        adminUserHasPermission('edit');

        $taskSchedule->update($request->validated());

        return response()->json(['message' => __('Cron updated successfully'), 'reload' => true]);
    }

    public function destroy(TaskSchedule $taskSchedule)
    {
        adminUserHasPermission('delete');
        if ($taskSchedule->type == 'default') {
            return response()->json(['message' => __('Default task cannot be deleted!')], 400);
        }

        $taskSchedule->delete();

        return response()->json(['message' => __('Cron deleted successfully')]);
    }

    public function statusUpdate(TaskSchedule $taskSchedule)
    {
        adminUserHasPermission('edit');

        $taskSchedule->update(['status' => $taskSchedule->status == 'active' ? 'Inactive' : 'active']);

        return redirect()->back()->withSuccess(__('Cron status updated successfully'));
    }

    public function runNow(TaskSchedule $taskSchedule)
    {
        $start_at = now();
        adminUserHasPermission('edit');
        $user = auth('admin')->user();
        try {
            DB::beginTransaction();
            Artisan::call($taskSchedule->command);
            $taskSchedule->update(['next_run' => $start_at->addSeconds((int) $taskSchedule?->schedule_time?->interval)]);
            $taskSchedule->update(['last_run' => now()]);
            ScheduleLogEvent::dispatch($taskSchedule, [
                'started_at' => $start_at,
                'ended_at' => now(),
                'duration' => now()->diffInSeconds($start_at),
                'data' => null,
            ]);
            event(new NotificationEvent($user, NotifyEventType::SCHEDULE, 'Cron success', 'Cron successfully run '.$taskSchedule->name));
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            ScheduleLogEvent::dispatch($taskSchedule, [
                'started_at' => $start_at,
                'ended_at' => now(),
                'duration' => now()->diffInSeconds($start_at),
                'data' => $th->getMessage(),
            ]);
            event(new NotificationEvent($user, NotifyEventType::SCHEDULE, 'Cron failed', 'Cron failed to run '.$taskSchedule->name));

            return redirect()->back()->withError($th->getMessage());
        }

        return redirect()->back()->withSuccess(__('Cron run successfully'));
    }

    public function getLogs(TaskSchedule $taskSchedule)
    {
        adminUserHasPermission('edit');

        $buttons = [
            [
                'label' => __('Back'),
                'icon' => 'ph ph-arrow-left',
                'type' => 'link',
                'link' => route('admin.settings.task-schedules.index'),
            ],
            [
                'label' => __('Remove Logs'),
                'icon' => 'ph ph-trash',
                'type' => 'link',
                'link' => route('admin.settings.task-schedules.remove-logs', $taskSchedule),
                'class' => 'bg-red-500 text-red-500 border-red-500 hover:bg-white hover:text-red-500',
            ],
        ];

        $logs = ModalIndexQuey::get(model: $taskSchedule->schedule_logs());

        $columns = [
            [
                'label' => __('Title'),
                'key' => 'title',
                'is_sortable' => true,
            ],
            [
                'label' => __('Started at'),
                'key' => 'started_at',
                'render' => function ($log) {
                    return Carbon::parse($log->started_at)->format('Y-m-d H:i:s');
                },
            ],
            [
                'label' => __('Ended at'),
                'key' => 'ended_at',
                'render' => function ($log) {
                    return Carbon::parse($log->ended_at)->format('Y-m-d H:i:s');
                },
            ],
            [
                'label' => __('Duration'),
                'key' => 'duration',
            ],
            [
                'label' => __('Log Message'),
                'key' => 'data',
            ],
        ];

        return view('admin.pages.settings.cron-schedules.logs', compact('taskSchedule', 'logs', 'columns', 'buttons'));
    }

    public function removeLogs(TaskSchedule $taskSchedule)
    {
        adminUserHasPermission('edit');

        $taskSchedule->schedule_logs()->delete();

        return redirect()->back()->with('success', __('Cron logs removed successfully'));
    }
}
