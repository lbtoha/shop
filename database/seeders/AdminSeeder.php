<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\AdminRole;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin_role = AdminRole::create([
            'name' => 'Admin',
            'caps' => array_keys(config('caps.permissions')),
            'module_caps' => array_map(fn ($menu) => $menu['link'], getMenuCaps(config('menu.admin.menu'))),
            'is_supper_admin' => true,
        ]);

        Admin::create([
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345678'),
            'admin_role_id' => $admin_role->id,
        ]);
    }
}
