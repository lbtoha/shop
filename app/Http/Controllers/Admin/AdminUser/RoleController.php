<?php

namespace App\Http\Controllers\Admin\AdminUser;

use App\Exceptions\CustomWebException;
use App\Http\Controllers\Controller;
use App\Models\AdminRole;
use App\Services\ModalIndexQuey;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        adminUserHasPermission(permission: 'read');

        $buttons = [
            [
                'label' => __('Create New Role'),
                'icon' => 'ph ph-plus',
                'type' => 'link',
                'link' => route('admin.admin-roles.create'),
            ],
        ];

        $roles = ModalIndexQuey::get(AdminRole::query());

        $columns = [
            [
                'label' => __('Name'),
                'key' => 'name',
                'header_class' => 'lg:w-[350px]',
            ],

            [
                'label' => __('Action'),
                'header_class' => 'flex justify-end',
                'render' => function ($role) {
                    $action_buttons = [
                        [
                            'label' => __('Edit'),
                            'icon' => 'ph ph-pencil',
                            'type' => 'link',
                            'href' => route('admin.admin-roles.edit', $role->id),
                        ],
                        [
                            'label' => __('Delete'),
                            'icon' => 'ph ph-trash',
                            'type' => 'delete',
                            'href' => route('admin.admin-roles.destroy', $role->id),
                        ],
                    ];

                    return view('admin.components.table-action', compact('action_buttons'))->render();
                },
            ],
        ];

        return view('admin.pages.admin.roles.index', compact('buttons', 'roles', 'columns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        adminUserHasPermission(permission: 'create');

        $buttons = [
            [
                'label' => __('Back'),
                'icon' => 'ph ph-back',
                'type' => 'link',
                'link' => route('admin.admin-roles.index'),
            ],
        ];

        return view('admin.pages.admin.roles.create', compact('buttons'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        adminUserHasPermission(permission: 'read');
        $validated = $request->validate([
            'name' => 'required|max:255',
            'caps' => 'required',
            'caps.*' => 'required',
            'module_caps' => 'required',
            'module_caps.*' => 'required',
        ]);

        $validated['module_caps'] = $this->getMenus($validated['module_caps']);

        AdminRole::create($validated);

        return response()->json(['message' => __('Role created successfully'), 'redirect' => route('admin.admin-roles.index')]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AdminRole $adminRole)
    {

        adminUserHasPermission(permission: 'edit');

        $buttons = [
            [
                'label' => __('Back'),
                'icon' => 'ph ph-back',
                'type' => 'link',
                'link' => route('admin.admin-roles.index'),
            ],
        ];

        return view('admin.pages.admin.roles.edit', compact('buttons', 'adminRole'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AdminRole $adminRole)
    {
        $user = auth('admin')->user();

        if ($adminRole->is_supper_admin && $user->admin_role_id !== $adminRole->id) {
            throw new CustomWebException(__('You can not update super admin role'));
        }

        adminUserHasPermission(permission: 'edit');
        $validated = $request->validate([
            'name' => 'required|max:255',
            'caps' => 'required',
            'caps.*' => 'required',
            'module_caps' => 'required',
            'module_caps.*' => 'required',
        ]);

        $validated['module_caps'] = $this->getMenus($validated['module_caps']);

        $adminRole->update($validated);

        return response()->json(['message' => __('Role updated successfully'), 'reload' => true]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AdminRole $adminRole)
    {
        if ($adminRole->is_supper_admin) {
            return response()->json(['message' => __('You can not delete super admin role')]);
        }

        adminUserHasPermission(permission: 'delete');
        $adminRole->delete();

        return response()->json(['message' => __('Role deleted successfully'), 'reload' => true]);
    }

    private function getMenus($menus = [])
    {
        $temp = [];

        foreach ($menus as $menu) {
            if (isset($menu['submenus'])) {
                $temp = [...$temp, ...$this->getMenus($menu['submenus'])];
            } elseif (is_array($menu)) {
                $temp = [...$temp, ...$this->getMenus($menu)];
            } else {
                $temp[] = $menu;
            }
        }

        return $temp;
    }
}
