<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Active Buyers') }}" :tab_buttons="$tab_buttons" />
        <x-admin::table :columns="$columns" :data="$buyers" />
    </div>
</x-admin-app-layout>
