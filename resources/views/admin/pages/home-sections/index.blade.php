<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Home Sections') }}" :buttons="$buttons" :isFilterable="false" />

        <p class="text-sm text-gray-400 mb-4">
            <i class="ph ph-info"></i>
            {{ __('Drag the handle to reorder sections. Use the toggle to show or hide a section instantly.') }}
        </p>

        <div id="home-sections-sortable" data-reorder-url="{{ route('admin.home-sections.reorder') }}">
            <x-admin::table :columns="$columns" :data="$sections" :is_pagination="false" />
        </div>
    </div>

    @push('scripts')
        @vite('resources/admin/js/home-section/index.js')
    @endpush
</x-admin-app-layout>
