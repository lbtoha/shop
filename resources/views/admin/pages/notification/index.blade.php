<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Notifications') }}" :buttons="$buttons" />
        <x-admin::table :columns="$columns" :data="$notifications" />
    </div>
</x-admin-app-layout>
