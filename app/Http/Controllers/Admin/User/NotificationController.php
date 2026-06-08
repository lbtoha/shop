<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Jobs\SendNotificationToUsers;
use App\Models\NotificationTemplate;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $send_types = [
        'all' => 'All Users',
        'email_verified' => 'Email Verified Users',
        'phone_verified' => 'Phone Verified Users',
        'kyc_verified' => 'KYC Verified Users',
    ];

    public function index()
    {
        adminUserHasPermission(permission: 'read');

        $buttons = [
            [
                'label' => __('Back'),
                'type' => 'link',
                'icon' => 'ph ph-back',
                'link' => route('admin.users.index'),
            ],
        ];

        $user_id = request('user_id');

        $templates = NotificationTemplate::active()->where('type', '!=', 'default')->select('id', 'name')->get();

        $send_types = $this->send_types;

        return view('admin.pages.user-manage.send-notification', compact('templates', 'user_id', 'buttons', 'send_types'));
    }

    public function store(Request $request)
    {
        adminUserHasPermission(permission: 'create');

        $request->validate([
            'template_id' => 'nullable|exists:notification_templates,id',
            'type' => 'required|in:email,sms',
            'user_send_type' => 'required|in:'.implode(',', array_keys($this->send_types)),
            'user_id' => 'nullable|exists:users,id',
            'subject' => 'nullable',
            'message' => 'nullable',
        ]);

        if (! $request->template_id && ! $request->subject && ! $request->message) {
            return response()->json(['message' => __('Please enter subject and message or select email template')], 422);
        }

        $subject = $request->subject ?? '';

        $message = inputSanitize($request->message ?? '');

        $type = $request->type;

        if ($request->template_id) {
            $template = NotificationTemplate::find($request->template_id);

            $body = $template->bodies()->where('channel', $type)->first();

            $subject = $body->subject;

            $message = $body->body;

        }

        if (! $subject && ! $message) {
            return response()->json(['message' => __('Please enter subject and message or select email template')], 422);
        }

        SendNotificationToUsers::dispatch(
            $type,
            $request->user_send_type,
            $subject,
            $message
        );

        return response()->json(['message' => __(ucfirst($type).' sending process started')]);
    }
}
