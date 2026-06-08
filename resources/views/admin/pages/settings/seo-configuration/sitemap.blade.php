<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Sitemap') }}" :buttons="$buttons" :isFilterable="false" />
        <form action="{{ route('admin.settings.seo.sitemap') }}" method="POST"
            class="space-y-4 xl:space-y-8 form-submit-add">
            @csrf
            <div class="editorContainer" data-input="sitemap"></div>
            <input type="hidden" name="site_map" id="sitemap" value="{{ getOption('sitemap') }}">

            <div class="flex items-center justify-end mt-4">
                <x-admin::primary-button type="submit">
                    {{ __('Save') }}
                </x-admin::primary-button>
            </div>
        </form>
    </div>
    @push('scripts')
        @vite('resources/admin/js/settings/sitemap.js')
    @endpush
</x-admin-app-layout>
