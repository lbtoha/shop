<?php

namespace App\Http\Controllers\Admin\Settings\Notification;

use App\Enums\NotificationType;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationTemplate;
use App\Models\User;
use App\Services\ModalIndexQuey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ServicesController extends Controller
{
    public function index()
    {
        adminUserHasPermission(permission: 'read');

        $service = request()->get('service', 'global-template');

        $buttons = [
            [
                'title' => __('Global Template'),
                'link' => route('admin.settings.notification.services', ['service' => 'global-template']),
                'icon' => 'ph ph-globe',
            ],
            [
                'title' => __('Email Settings'),
                'link' => route('admin.settings.notification.services', ['service' => 'mail']),
                'icon' => 'ph ph-envelope',
            ],
            [
                'title' => __('SMS Settings'),
                'link' => route('admin.settings.notification.services', ['service' => 'sms']),
                'icon' => 'ph ph-chat',
            ],
            [
                'title' => __('Broadcast Notification Settings'),
                'link' => route('admin.settings.notification.services', ['service' => 'push']),
                'icon' => 'ph ph-bell-simple-ringing',
            ],
            [
                'title' => __('Template'),
                'icon' => 'ph ph-bell',
                'link' => route('admin.settings.notification.services', ['service' => 'template']),
            ],
        ];

        if ($service == 'sms') {
            return view('admin.pages.settings.notifications.index', ['service' => $service, 'buttons' => $buttons]);
        }

        if ($service == 'email') {
            return view('admin.pages.settings.notifications.index', ['service' => $service, 'buttons' => $buttons]);
        }

        if ($service == 'push') {
            return view('admin.pages.settings.notifications.index', ['service' => $service, 'buttons' => $buttons]);
        }

        if ($service == 'template') {
            $templates = ModalIndexQuey::get(model: NotificationTemplate::query()->where('type', '!=', NotificationType::DEFAULT->value));

            $template_buttons = [
                [
                    'label' => __('Add New Template'),
                    'icon' => 'ph ph-plus',
                    'type' => 'link',
                    'link' => route('admin.settings.notification.templates.create'),
                ],
            ];

            $columns = [
                [
                    'label' => __('Name'),
                    'key' => 'name',
                    'is_sortable' => true,
                ],
                [
                    'label' => __('Subject'),
                    'key' => 'subject',
                    'is_sortable' => true,
                    'render' => function ($template) {
                        return $template->subject;
                    },
                ],
                [
                    'label' => __('Status'),
                    'key' => 'status',
                    'is_sortable' => true,
                    'render' => function ($template) {
                        return '<span class="status '.($template->status == 'active' ? 'success' : 'danger').'">'.$template->status.'</span>';
                    },
                ],
                [
                    'label' => __('Action'),
                    'render' => function ($template) {
                        $action_buttons = [
                            [
                                'label' => __('Edit'),
                                'icon' => 'ph ph-pencil',
                                'type' => 'link',
                                'href' => route('admin.settings.notification.templates.edit', $template->id),
                            ],
                        ];

                        return view('admin.components.table-action', compact('action_buttons'))->render();
                    },
                ],
            ];

            return view('admin.pages.settings.notifications.index', compact('buttons', 'template_buttons', 'service', 'templates', 'columns'));
        }

        $defaultTemplate = NotificationTemplate::with('bodies')->default()->where('status', 'active')->first();

        return view('admin.pages.settings.notifications.index', ['service' => $service, 'buttons' => $buttons, 'defaultTemplate' => $defaultTemplate]);
    }

    public function store(Request $request, $service)
    {
        adminUserHasPermission(permission: 'edit');
        if ($service == 'sms') {

            $validated = $request->validate([
                'driver' => 'required|in:twilio,nexmo',
                'account_sid' => 'required_if:driver,twilio',
                'auth_token' => 'required_if:driver,twilio',
                'from' => 'required',
                'api_key' => 'required_if:driver,nexmo',
                'api_secret' => 'required_if:driver,nexmo',
            ]);

            $data = [];

            switch ($validated['driver']) {
                case 'twilio':
                    $data = [
                        'driver' => 'twilio',
                        'account_sid' => $validated['account_sid'],
                        'auth_token' => $validated['auth_token'],
                        'from' => $validated['from'],
                    ];
                    break;
                case 'nexmo':
                    $data = [
                        'driver' => 'nexmo',
                        'api_key' => $validated['api_key'],
                        'api_secret' => $validated['api_secret'],
                        'from' => $validated['from'],
                    ];
                    break;
            }

            storeOption([
                'sms_config' => $data,
            ]);

            return response()->json(['message' => __('Settings updated successfully'), 'reload' => true]);
        }

        if ($service == 'broadcast') {
            $validated = $request->validate([
                'client_config_json' => 'required|string',
                'vapidKey' => 'required|string',
            ]);

            $jsonString = trim($validated['client_config_json']);

            // Convert JavaScript object syntax to valid JSON (add quotes to unquoted keys)
            $jsonString = preg_replace('/([{,]\s*)(\w+)\s*:/m', '$1"$2":', $jsonString);

            $clientConfig = json_decode($jsonString, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['message' => __('Invalid configuration format. Please paste the firebaseConfig object from Firebase Console.')], 422);
            }

            $requiredFields = ['apiKey', 'authDomain', 'projectId', 'storageBucket', 'messagingSenderId', 'appId'];

            foreach ($requiredFields as $field) {
                if (empty($clientConfig[$field])) {
                    return response()->json(['message' => __('Missing required field: :field', ['field' => $field])], 422);
                }
            }

            $clientData = [
                'apiKey' => $clientConfig['apiKey'],
                'authDomain' => $clientConfig['authDomain'],
                'projectId' => $clientConfig['projectId'],
                'storageBucket' => $clientConfig['storageBucket'],
                'messagingSenderId' => $clientConfig['messagingSenderId'],
                'appId' => $clientConfig['appId'],
                'measurementId' => $clientConfig['measurementId'] ?? null,
                'vapidKey' => $validated['vapidKey'],
            ];

            Storage::disk('firebase')->put('cloud-messaging.json', json_encode($clientData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return response()->json(['message' => __('Client configuration saved successfully'), 'reload' => true]);
        }

        $validated = $request->validate([
            'mailer' => 'required|in:smtp,mailgun,sendgrid,mailjet',
            'host' => 'required_if:mailer,smtp',
            'port' => 'required_if:mailer,smtp',
            'encryption' => 'required_if:mailer,smtp',
            'username' => 'required_if:mailer,smtp',
            'password' => 'required_if:mailer,smtp',
            // mailgun
            'domain' => 'required_if:mailer,mailgun',
            'secret' => 'required_if:mailer,mailgun',
            'endpoint' => 'required_if:mailer,mailgun',

            'mail_address' => 'required',
            'mail_name' => 'required',
        ]);

        $data = [];

        if ($validated['mailer'] == 'smtp') {
            $data = [
                'mailer' => $validated['mailer'],
                'host' => $validated['host'],
                'port' => $validated['port'],
                'encryption' => $validated['encryption'],
                'username' => $validated['username'],
                'password' => $validated['password'],
                'mail_address' => $validated['mail_address'],
                'mail_name' => $validated['mail_name'],
            ];
        }

        if ($validated['mailer'] == 'mailgun') {
            $data = [
                'mailer' => $validated['mailer'],
                'domain' => $validated['domain'],
                'secret' => $validated['secret'],
                'endpoint' => $validated['endpoint'],
                'mail_address' => $validated['mail_address'],
                'mail_name' => $validated['mail_name'],
            ];
        }

        storeOption([
            'email_config' => $data,
        ]);

        return response()->json(['message' => __('Settings updated successfully'), 'reload' => true]);
    }

    public function testEmailService(Request $request)
    {
        $validated = $request->validate([
            'send_to' => 'required|email',
            'subject' => 'required',
            'message' => 'required',
        ]);

        try {
            Mail::raw($validated['message'], function ($message) use ($validated) {
                $message->to($validated['send_to'])
                    ->subject($validated['subject']);
            });

            return response()->json(['message' => __('Email sent successfully')]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()]);
        }
    }

}
