<?php

namespace App\Http\Controllers\Admin\Notification;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\ModalIndexQuey;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        adminUserHasPermission('read');

        $buttons = [
            [
                'label' => __('Read All'),
                'icon' => 'ph ph-check-bold',
                'type' => 'link',
                'link' => route('admin.read-all-notification'),
            ],
            [
                'label' => __('Delete All'),
                'icon' => 'ph ph-trash',
                'type' => 'link',
                'link' => route('admin.delete-all-notification'),
                'style' => 'text-red-500 border-red-500 hover:bg-red-500 hover:text-white border-red-500',
            ],
        ];

        $notifications = ModalIndexQuey::get(Notification::query());

        $columns = [
            [
                'label' => __('Type'),
                'key' => 'type',
                'header_class' => 'lg:w-[350px]',
                'is_sortable' => true,
            ],
            [
                'label' => __('Content'),
                'key' => 'data',
                'header_class' => 'lg:w-[200px]',
            ],
            [
                'label' => __('Read At'),
                'key' => 'read_at',
                'is_sortable' => true,
                'render' => function ($notification) {
                    return $notification->read_at ? '<p class="s-text font-medium">'.Carbon::parse($notification->read_at)->format('Y-m-d H:i A').'</p>
                            <span class="text-xs">'.Carbon::parse($notification->read_at)->diffForHumans().'</span>' : '-';
                },
            ],
            [
                'label' => __('Action'),
                'render' => function ($notification) {
                    $action_buttons = [
                        [
                            'label' => __('Show'),
                            'icon' => 'ph ph-eye',
                            'type' => 'link',
                            'href' => route('admin.notifications.show', $notification->id),
                        ],
                        [
                            'label' => __('Delete'),
                            'icon' => 'ph ph-trash',
                            'type' => 'delete',
                            'href' => route('admin.notifications.destroy', $notification->id),
                        ],
                    ];

                    return view('admin.components.table-action', compact('action_buttons'))->render();
                },
            ],
        ];

        return view('admin.pages.notification.index', compact('buttons', 'notifications', 'columns'));
    }

    public function store(Request $request)
    {
        adminUserHasPermission('create');

        Notification::query()->whereNull('read_at')->update(['read_at' => now()]);

        return redirect()->back()->with('success', __('Notification read successfully'));
    }

    public function show(Notification $notification)
    {
        adminUserHasPermission('read');

        $notification->update(['read_at' => now()]);

        return redirect($notification->type->route($notification->notifiable_id));
    }

    public function destroy(Notification $notification)
    {
        adminUserHasPermission('delete');

        $notification->delete();

        return redirect()->back()->with('success', __('Notification deleted successfully'));
    }

    public function getLatestNotification()
    {
        adminUserHasPermission('read');

        $total_unread = Notification::where('read_at', null)->count();
        $total_notification = Notification::with('notifiable')->whereNull('read_at')->latest()->take(5)->get();

        return response()->json([
            'total_unread' => $total_unread,
            'total_notification' => $total_notification,
        ]);
    }

    public function readAllNotification()
    {
        adminUserHasPermission('edit');

        Notification::query()->where('read_at', null)->update(['read_at' => now()]);

        return redirect()->back()->withSuccess(__('Notification read successfully'));
    }

    public function deleteAllNotification()
    {
        adminUserHasPermission('delete');

        $ids = \request()->ids;

        if (isset($ids)) {
            Notification::destroy($ids);

            return redirect()->back()->with('success', __('Notification deleted successfully'));
        }

        Notification::query()->delete();

        return redirect()->back()->with('success', __('Notification deleted successfully'));
    }
}
