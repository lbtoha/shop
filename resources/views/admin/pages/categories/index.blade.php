<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Categories') }}" :buttons="$buttons" :isFilterable="true" />
        <x-admin::table :columns="$columns" :data="$categories" />
    </div>
</x-admin-app-layout>
