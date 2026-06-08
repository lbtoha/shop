<?php

namespace App\Http\Controllers\Admin\Settings\Notification;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NotificationTemplateRequest;
use App\Models\NotificationTemplate;
use App\Models\NotificationTemplateBody;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationTemplateController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        adminUserHasPermission(permission: 'create');

        $buttons = [
            [
                'label' => __('Back'),
                'icon' => 'ph ph-arrow-left',
                'type' => 'link',
                'link' => route('admin.settings.notification.services', ['service' => 'template']),
            ],
        ];

        return view('admin.pages.settings.notifications.templates.create', compact('buttons'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        adminUserHasPermission(permission: 'read');

        $request->validate([
            'name' => 'required',
            'bodies.*.channel' => 'required',
            'is_active' => 'required|in:1,0',
            'bodies.*.subject' => 'required',
            'bodies.*.body' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            $notification = NotificationTemplate::create([
                'name' => $request->name,
                'short_codes' => NotificationTemplate::default()->first()->short_codes,
            ]);

            $notification->bodies()->createMany($request->bodies);
        });

        return response()->json(['redirect' => route('admin.settings.notification.services', ['service' => 'template']), 'message' => __('Email Template created successfully')]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        adminUserHasPermission(permission: 'edit');

        $buttons = [
            [
                'label' => __('Back'),
                'icon' => 'ph ph-arrow-left',
                'type' => 'link',
                'link' => route('admin.settings.notification.services', ['service' => 'template']),
            ],
            [
                'label' => __('Add New Template'),
                'icon' => 'mdi:plus',
                'type' => 'link',
                'link' => route('admin.settings.notification.templates.create'),
            ],
        ];

        $notificationTemplate = NotificationTemplate::find($id);

        return view('admin.pages.settings.notifications.templates.edit', compact('notificationTemplate', 'buttons'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NotificationTemplateRequest $request, $id)
    {
        adminUserHasPermission(permission: 'edit');

        $notification = NotificationTemplateBody::find($id);

        $notification->update($request->validated());

        return response()->json(['message' => __('Email Template updated successfully')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NotificationTemplate $notificationTemplate)
    {
        adminUserHasPermission(permission: 'delete');

        $notificationTemplate->delete();

        return response()->json(['message' => __('Email Template deleted successfully')]);
    }

    public function changeStatus(NotificationTemplate $notificationTemplate)
    {
        adminUserHasPermission(permission: 'edit');

        $notificationTemplate->update([
            'status' => $notificationTemplate->status === 'active' ? 'inactive' : 'active',
        ]);

        return redirect()->back()->with('success', __('Status changed successfully'));
    }
}
