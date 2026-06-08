<?php

namespace App\Http\Controllers\Admin\Settings\Menu;

use App\Exceptions\CustomWebException;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Menu Item Controller for create and update
 * In this controller we can create and update menu items
 *
 * @singleMenuItemStore(Request $request)
 */
class MenuItemController extends Controller
{
    public function store(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'newItems.*.page_id' => 'nullable|exists:pages,id',
            'newItems.*.title' => 'required|string|max:255',
            'newItems.*.order' => 'required|integer',
        ]);

        $items = [];

        foreach ($validated['newItems'] as $item) {
            $items[] = MenuItem::create([
                'menu_id' => $menu->id,
                'page_id' => $item['page_id'] ?? null,
                'title' => $item['title'],
                'order' => $item['order'],
            ]);
        }

        Cache::forget('menu_list');

        return response()->json([
            'message' => __('Menu items created successfully'),
            'items' => $items,
        ]);
    }

    public function destroy(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'id' => 'required|exists:menu_items,id',
        ]);

        $item_menu = $menu->items()->where('id', $validated['id'])->where('is_primary', false)->first();

        if (is_null($item_menu)) {
            return response()->json(['message' => __('This menu item can not be deleted')], 404);
        }

        $item_menu->delete();

        Cache::forget('menu_list');

        return response()->json(['message' => __('Menu item deleted successfully')]);
    }

    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'id' => 'nullable',
            'page_id' => 'nullable|exists:pages,id',
            'parent_id' => 'nullable|exists:menu_items,id',
            'title' => 'required|string|max:255',
            'url' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'target' => 'nullable|string|max:255',
        ]);

        if ($request->id) {
            $menuItem = $menu->items()->find($request->id);

            if (is_null($menuItem)) {
                return response()->json(['message' => __('Menu item not found')], 404);
            }

            $menuItem->update($validated);

            Cache::forget('menu_list');
        }

        return response()->json(['message' => __('Menu item saved successfully')]);
    }

    public function bulkMenuItemUpdate(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'menu_title' => 'required|string|max:255',
            'menu_location' => 'required|string|max:255',
            'menu_items.*.id' => 'required|exists:menu_items,id',
            'menu_items.*.page_id' => 'nullable|exists:pages,id',
            'menu_items.*.parent_id' => 'nullable|exists:menu_items,id',
            'menu_items.*.title' => 'required|string|max:255',
            'menu_items.*.url' => 'nullable|string|max:255',
            'menu_items.*.icon' => 'nullable|string|max:255',
            'menu_items.*.order' => 'nullable|integer',
            'menu_items.*.target' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();
            foreach ($validated['menu_items'] as $menuItem) {

                $item = $menu->items()->find($menuItem['id']);

                if (is_null($menuItem)) {
                    return response()->json(['message' => __('Menu item not found')], 404);
                }

                $item->update($menuItem);
            }

            $exists = Menu::where('id', '!=', $menu->id)->where('status', 'active')
                ->where('location', $validated['menu_location'])->exists();

            if ($exists) {
                throw new CustomWebException(__('You can only have 1 active Menu List per location'));
            }

            $menu->update([
                'name' => $validated['menu_title'],
                'location' => $validated['menu_location'],
            ]);

            Cache::forget('menu_list');

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            return response()->json(['message' => $exception->getMessage()], 500);
        }

        return response()->json(['message' => __('Menu items saved successfully')]);
    }
}
