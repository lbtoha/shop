<?php

namespace App\Http\Controllers\Admin\AdminUser;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRequest;
use App\Models\Admin;
use App\Models\AdminRole;
use App\Services\ModalIndexQuey;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        adminUserHasPermission(permission: 'read');

        $buttons = [
            [
                'label' => __('Create New Admin User'),
                'icon' => 'ph ph-plus',
                'type' => 'link',
                'link' => route('admin.admins.create'),
            ],
        ];

        $admins = ModalIndexQuey::get(Admin::query());

        $columns = [
            [
                'label' => __('Name'),
                'key' => 'first_name',
                'header_class' => 'lg:w-[350px]',
                'render' => fn ($admin) => '<span class="s-text font-medium">'.$admin->full_name.'</span>',
            ],
            [
                'label' => __('Email'),
                'key' => 'email',
                'header_class' => 'lg:w-[200px]',
            ],
            [
                'label' => __('Phone'),
                'key' => 'phone',
            ],
            [
                'label' => __('Role'),
                'key' => 'role',
                'render' => fn ($admin) => '<span class="s-text font-medium">'.$admin?->role?->name.'</span>',
            ],
            [
                'label' => __('Action'),
                'render' => function ($admin) {
                    $action_buttons = [
                        [
                            'label' => __('Edit'),
                            'icon' => 'ph ph-pencil',
                            'type' => 'link',
                            'href' => route('admin.admins.edit', $admin->id),
                        ],
                        [
                            'label' => __('Delete'),
                            'icon' => 'ph ph-trash',
                            'type' => 'delete',
                            'href' => route('admin.admins.destroy', $admin->id),
                        ],
                    ];

                    return view('admin.components.table-action', compact('action_buttons'))->render();
                },
            ],
        ];

        return view('admin.pages.admin.index', compact('buttons', 'admins', 'columns'));
    }

    public function create()
    {
        adminUserHasPermission(permission: 'create');

        $buttons = [
            [
                'label' => __('Back'),
                'icon' => 'lets-icons:back',
                'type' => 'link',
                'link' => route('admin.admins.index'),
            ],
        ];

        $roles = AdminRole::select('id', 'name')->get();

        return view('admin.pages.admin.create', compact('buttons', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminRequest $request)
    {
        adminUserHasPermission(permission: 'create');

        Admin::create($request->validated());

        return response()->json(['message' => __('User created successfully'), 'reload' => true]);
    }

    public function edit(Admin $admin)
    {
        adminUserHasPermission(permission: 'edit');

        $buttons = [
            [
                'label' => __('Back'),
                'icon' => 'lets-icons:back',
                'type' => 'link',
                'link' => route('admin.admins.index'),
            ],
        ];

        $roles = AdminRole::select('id', 'name')->get();

        return view('admin.pages.admin.edit', compact('admin', 'buttons', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminRequest $request, Admin $admin)
    {
        adminUserHasPermission(permission: 'edit');

        $admin->update($request->validated());

        return response()->json(['message' => __('User updated successfully'), 'reload' => true]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        adminUserHasPermission(permission: 'delete');

        if ($admin->role->is_supper_admin) {
            return redirect()->back()->withError(__('You can not delete super admin'));
        }

        $admin->delete();

        return redirect()->back()->withSuccess(__('User deleted successfully'));
    }
}
