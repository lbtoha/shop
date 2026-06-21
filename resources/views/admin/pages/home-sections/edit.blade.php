<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Edit Home Section') }}" :buttons="$buttons" :isFilterable="false" />
        @include('admin.pages.home-sections._form')
    </div>
</x-admin-app-layout>
