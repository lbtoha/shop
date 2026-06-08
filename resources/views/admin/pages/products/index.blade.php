<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Products') }}" :buttons="$buttons" :isFilterable="true" />
        <x-admin::table :columns="$columns" :data="$products" />
    </div>
</x-admin-app-layout>
