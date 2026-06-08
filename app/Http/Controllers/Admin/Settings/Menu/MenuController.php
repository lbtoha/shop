<?php

namespace App\Http\Controllers\Admin\Settings\Menu;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Services\ModalIndexQuey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MenuController extends Controller
{
    public function index()
    {
        $menu_add_modal_id = 'menu_add_modal_id';
        $menu_edit_modal_id = 'menu_edit_modal_id';
        $buttons = [
            [
                'label' => __('Add New Menu'),
                'icon' => 'ph ph-plus-bold',
                'type' => 'modal',
                'id' => $menu_add_modal_id,
                'href' => route('admin.settings.menus.store'),
            ],
        ];

        $menus = ModalIndexQuey::get(model: Menu::query());

        $columns = [
            [
                'label' => __('Title'),
                'key' => 'name',
                'header_class' => 'lg:w-[350px]',
                'is_sortable' => true,
            ],
            [
                'label' => __('Location'),
                'key' => 'location',
                'render' => fn ($modal) => $modal->location->label(),
            ],
            [
                'label' => __('Status'),
                'key' => 'status',
                'is_sortable' => true,
                'render' => function ($menu) {
                    $color = $menu->status == 'active' ? 'success' : 'danger';

                    return '<span class="status '.$color.' capitalize">'.__($menu->status).'</span>';
                },
            ],
            [
                'label' => __('Action'),
                'render' => function ($menu) use ($menu_edit_modal_id) {
                    $action_buttons = [
                        [
                            'label' => __('Edit'),
                            'icon' => 'ph ph-pencil',
                            'id' => $menu_edit_modal_id,
                            'row' => $menu,
                            'type' => 'modal',
                            'href' => route('admin.settings.menus.update', $menu->id),
                        ],
                        [
                            'label' => __('Delete'),
                            'icon' => 'ph ph-trash',
                            'type' => 'delete',
                            'href' => route('admin.settings.menus.destroy', $menu->id),
                        ],
                        [
                            'label' => $menu->status == 'active' ? __('InActive') : __('Active'),
                            'icon' => $menu->status == 'active' ? 'ph ph-toggle-left' : 'ph ph-toggle-right',
                            'type' => 'link',
                            'href' => route('admin.settings.menus.change-status', $menu->id),
                        ],
                        [
                            'label' => __('Customize Menu'),
                            'icon' => 'ph ph-menu-right',
                            'type' => 'link',
                            'href' => route('admin.settings.menus.edit', $menu->id),
                        ],
                    ];

                    return view('admin.components.table-action', compact('action_buttons'))->render();
                },
            ],
        ];

        return view('admin.pages.settings.menu.index', compact('menus', 'columns', 'buttons', 'menu_add_modal_id', 'menu_edit_modal_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        Menu::create([
            'name' => $request->name,
            'status' => 'inactive',
        ]);

        Cache::forget('menu_list');

        return response()->json(['message' => __('Menu created successfully'), 'reload' => true]);
    }

    public function edit(Menu $menu)
    {
        $pages = collect();

        $items = $menu->items()->parent()->with('children')->get();

        return view('admin.pages.settings.menu.edit-menu-items', compact('menu', 'pages', 'items'));
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $menu->update([
            'name' => $request->name,
        ]);

        Cache::forget('menu_list');

        return response()->json(['message' => __('Menu updated successfully')]);
    }

    public function changeStatus(Menu $menu)
    {
        adminUserHasPermission(permission: 'edit');

        $menu->update([
            'status' => $menu->status == 'active' ? 'inactive' : 'active',
        ]);

        Cache::forget('menu_list');

        return back()->withSuccess(__('Status changed successfully'));
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();

        Cache::forget('menu_list');

        return response()->json(['message' => __('Menu deleted successfully')]);
    }
}
