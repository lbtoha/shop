<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Coupons') }}" :buttons="$buttons" :isFilterable="true" />
        <x-admin::table :columns="$columns" :data="$coupons" />
    </div>
</x-admin-app-layout>
