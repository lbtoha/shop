<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExtensionController extends Controller
{
    public function index()
    {
        adminUserHasPermission(permission: 'read');

        foreach (config('extension') as $key => $extension) {
            $extensions[] = [
                ...$extension,
                'slug' => $key,
            ];
        }

        $columns = [
            [
                'label' => __('Name'),
                'key' => 'name',
            ],
            [
                'label' => __('Icon'),
                'key' => 'icon',
                'header_class' => 'lg:w-[200px]',
                'render' => fn ($extension) => '<i class="'.$extension['icon'].' text-2xl"></i>',
            ],
            [
                'label' => __('Status'),
                'key' => 'status',
                'is_sortable' => true,
                'render' => function ($extension) {
                    $color = $extension['is_enabled'] ? 'success' : 'danger';

                    return '<span class="status '.$color.' capitalize">'.($extension['is_enabled'] ? __('Enabled') : __('Disabled')).'</span>';
                },
            ],
            [
                'label' => __('Action'),
                'render' => function ($extension) {
                    $action_buttons = [
                        [
                            'label' => __('Edit'),
                            'icon' => 'ph ph-pencil',
                            'id' => $extension['slug'],
                            'row' => json_encode($extension),
                            'type' => 'modal',
                            'href' => route('admin.settings.extensions.update', $extension['slug']),
                        ],

                        [
                            'label' => $extension['is_enabled'] ? __('InActive') : __('Active'),
                            'icon' => $extension['is_enabled'] ? 'ph ph-toggle-left' : 'ph ph-toggle-right',
                            'type' => 'link',
                            'href' => route('admin.settings.extensions.enable', $extension['slug']),
                        ],
                    ];

                    return view('admin.components.table-action', compact('action_buttons'))->render();
                },
            ],

        ];

        return view('admin.pages.settings.extensions.index', compact('extensions', 'columns'));
    }

    public function update(Request $request, $slug)
    {
        adminUserHasPermission(permission: 'edit');
        if ($slug == 'google_analytics') {

            $validated = $request->validate([
                'measurement_id' => 'required',
            ]);

            $extension = config('extension.google_analytics');

            $extension['measurement_id'] = $validated['measurement_id'];

            storeOption([
                'extension_'.$slug => $extension,
            ]);
        }

        if ($slug == 'recaptcha') {

            $validated = $request->validate([
                'site_key' => 'required',
                'secret_key' => 'required',
            ]);

            $extension = config('extension.recaptcha');

            $extension['site_key'] = $validated['site_key'];
            $extension['secret_key'] = $validated['secret_key'];

            storeOption([
                'extension_'.$slug => $extension,
            ]);

        }

        if ($slug == 'tawk_to') {

            $validated = $request->validate([
                'property_id' => 'required',
                'widget_id' => 'required',
            ]);

            $extensions = config('extension.tawk_to');

            $extensions['property_id'] = $validated['property_id'];
            $extensions['widget_id'] = $validated['widget_id'];

            storeOption([
                'extension_'.$slug => $extensions,
            ]);

        }

        return response()->json(['message' => __('Settings updated successfully')]);
    }

    public function enable(Request $request, $slug)
    {
        adminUserHasPermission(permission: 'edit');
        $extension = config('extension.'.$slug);
        $extension['is_enabled'] = ! $extension['is_enabled'];
        storeOption([
            'extension_'.$slug => $extension,
        ]);

        return back()->withSuccess(__('Settings updated successfully'));
    }
}
