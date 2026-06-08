<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Hero Banners') }}" :buttons="$buttons" :isFilterable="true" />
        <x-admin::table :columns="$columns" :data="$banners" />
    </div>
</x-admin-app-layout>
