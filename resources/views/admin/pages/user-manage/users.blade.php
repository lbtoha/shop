<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Active Users') }}" :tab_buttons="$tab_buttons" />
        <x-admin::table :columns="$columns" :data="$users" />
    </div>
</x-admin-app-layout>
