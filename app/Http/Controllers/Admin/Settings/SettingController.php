<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function __invoke()
    {
        $search = request('search', '');

        $parent = request('parent', '');

        $all_menus = config('menu.admin.menu');

        $authorizedMenus = authorizedMenus($all_menus, auth('admin')->user());

        $settings_menu = collect($authorizedMenus)
            ->first(fn ($menu) => isset($menu['parent']) && $menu['parent'] === 'admin/settings');

        if (! $settings_menu || empty($settings_menu['submenus'])) {
            return view('admin.pages.settings.index', ['menus' => []]);
        }

        $setting_menus = $settings_menu['submenus'];

        if ($parent) {
            $setting_menus = array_filter($setting_menus, fn ($menu) => $menu['parent'] === $parent);
        }

        $setting_menus = $this->returnFlatMenu($setting_menus);

        if ($search) {
            $setting_menus = array_filter($setting_menus, fn ($menu) => str_contains(strtolower($menu['title']), strtolower($search))
            );
        }

        return view('admin.pages.settings.index', ['menus' => $setting_menus]);
    }

    private function returnFlatMenu(array $menu): array
    {
        $result = [];
        foreach ($menu as $item) {
            if (isset($item['submenus'])) {
                $result = array_merge($result, $this->returnFlatMenu($item['submenus']));
            } else {
                if (isset($item['link'])) {
                    $item['link'] = route($item['link']);
                }
                $result[] = $item;

            }

        }

        return $result;
    }
}
