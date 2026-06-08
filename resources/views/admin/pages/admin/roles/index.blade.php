<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Admin Roles') }}" :buttons="$buttons" />
        <x-admin::table :columns="$columns" :data="$roles" />
    </div>
    @push('scripts')
        @vite('resources/admin/js/admin-user/roles.js')
    @endpush
</x-admin-app-layout>
