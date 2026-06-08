<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Login Log Reports') }}" :buttons="$buttons" />
        <x-admin::table :columns="$columns" :data="$logs"  />
    </div>
</x-admin-app-layout>
