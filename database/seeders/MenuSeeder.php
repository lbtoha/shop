<?php

namespace Database\Seeders;

use App\Enums\MenuLocationEnum;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            [
                'name' => 'Quick Links',
                'slug' => 'quick-links',
                'location' => MenuLocationEnum::QUICK_LINKS,
            ],
            [
                'name' => 'Resources Menu',
                'slug' => 'resources-menu',
                'location' => MenuLocationEnum::RESOURCES_MENU,
            ],
        ];

        foreach ($menus as $menuItem) {
            Menu::create([
                'name' => $menuItem['name'],
                'slug' => $menuItem['slug'],
                'location' => $menuItem['location'],
            ]);
        }

        // Create or update the header menu
        $headerMenu = Menu::updateOrCreate(
            ['slug' => 'header-menu'],
            [
                'name' => 'Main Menu',
                'location' => MenuLocationEnum::HEADER,
                'status' => 'active',
            ]
        );

        // Clear existing menu items if any
        MenuItem::where('menu_id', $headerMenu->id)->delete();

        // Define the menu structure
        $menuItems = [
            [
                'title' => 'About Us',
                'url' => '/about-us',
                'order' => 1,
            ],
            [
                'title' => 'Contact Us',
                'url' => '/contact-us',
                'order' => 2,
            ],
        ];

        // Create menu items
        foreach ($menuItems as $item) {
            $this->createMenuItem($headerMenu->id, $item);
        }
    }

    private function createMenuItem(int $menuId, array $item, ?int $parentId = null): void
    {
        $menuItem = MenuItem::create([
            'menu_id' => $menuId,
            'parent_id' => $parentId,
            'title' => $item['title'],
            'url' => $item['url'] ?? null,
            'order' => $item['order'],
            'target' => '_self',
        ]);

        if (! empty($item['children'])) {
            foreach ($item['children'] as $child) {
                $this->createMenuItem($menuId, $child, $menuItem->id);
            }
        }
    }
}
