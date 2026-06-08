<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Orders') }}" :tab_buttons="$tab_buttons" :isFilterable="true" />
        <x-admin::table :columns="$columns" :data="$orders" />
    </div>
</x-admin-app-layout>
