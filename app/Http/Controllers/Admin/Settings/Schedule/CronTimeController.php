<?php

namespace App\Http\Controllers\Admin\Settings\Schedule;

use App\Http\Controllers\Controller;
use App\Models\ScheduleTime;
use App\Services\ModalIndexQuey;
use Illuminate\Http\Request;

class CronTimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        adminUserHasPermission('read');

        $buttons = [
            [
                'label' => __('Back'),
                'icon' => 'ph ph-arrow-left',
                'type' => 'link',
                'link' => route('admin.settings.task-schedules.index'),
            ],
            [
                'label' => __('Add new Cron'),
                'icon' => 'ph ph-plus-bold',
                'type' => 'modal',
                'id' => 'schedule_time_create_modal',
                'href' => route('admin.settings.schedule-times.store'),
            ],
        ];

        $schedule_times = ModalIndexQuey::get(model: ScheduleTime::query());

        $columns = [
            [
                'label' => __('Name'),
                'key' => 'name',
                'is_sortable' => true,
            ],
            [
                'label' => __('Interval'),
                'key' => 'interval',
                'is_sortable' => true,
            ],
            [
                'label' => __('Action'),
                'render' => function ($cron) {
                    $action_buttons = [
                        [
                            'label' => __('Edit'),
                            'icon' => 'ph ph-pencil',
                            'id' => 'schedule_time_edit_modal',
                            'row' => $cron,
                            'type' => 'modal',
                            'href' => route('admin.settings.schedule-times.update', $cron->id),
                        ],
                        [
                            'label' => __('Delete'),
                            'icon' => 'ph ph-trash',
                            'type' => 'delete',
                            'class' => 'form-submit-delete',
                            'href' => route('admin.settings.schedule-times.destroy', $cron->id),
                        ],
                    ];

                    return view('admin.components.table-action', compact('action_buttons'))->render();
                },
            ],
        ];

        return view('admin.pages.settings.cron-schedules.schedule-time', compact('schedule_times', 'columns', 'buttons'));
    }

    public function store(Request $request)
    {
        adminUserHasPermission('create');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'interval' => 'required|string|max:255',
        ]);

        ScheduleTime::create($validated);

        return response()->json(['message' => __('Cron time created successfully'), 'reload' => true]);
    }

    public function update(Request $request, ScheduleTime $scheduleTime)
    {
        adminUserHasPermission('edit');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'interval' => 'required|string|max:255',
        ]);

        $scheduleTime->update($validated);

        return response()->json(['message' => __('Cron time updated successfully'), 'reload' => true]);
    }

    public function destroy(ScheduleTime $scheduleTime)
    {
        adminUserHasPermission('delete');

        $count = $scheduleTime->tasks->where('type', 'default')->count();

        if ($count > 0) {
            return response()->json(['message', __('Cron time cannot be deleted as it is in use on a Default Task')], 400);
        }

        $scheduleTime->delete();

        return response()->json(['message', __('Cron time deleted successfully')]);
    }
}
