<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Admin Users') }}" :buttons="$buttons" />
        <x-admin::table :columns="$columns" :data="$admins" />
    </div>
</x-admin-app-layout>
