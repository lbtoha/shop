<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $roles = DB::table('admin_roles')->get();
        foreach ($roles as $role) {
            $caps = json_decode($role->module_caps, true) ?? [];
            if (in_array('admin.settings.logo-favicon.index', $caps) && !in_array('admin.settings.brand-partners.index', $caps)) {
                $caps[] = 'admin.settings.brand-partners.index';
                DB::table('admin_roles')
                    ->where('id', $role->id)
                    ->update(['module_caps' => json_encode($caps)]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $roles = DB::table('admin_roles')->get();
        foreach ($roles as $role) {
            $caps = json_decode($role->module_caps, true) ?? [];
            if (($key = array_search('admin.settings.brand-partners.index', $caps)) !== false) {
                unset($caps[$key]);
                DB::table('admin_roles')
                    ->where('id', $role->id)
                    ->update(['module_caps' => json_encode(array_values($caps))]);
            }
        }
    }
};
