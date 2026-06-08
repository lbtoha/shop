<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Robots') }}" :buttons="$buttons" :isFilterable="false" />
        <form action="{{ route('admin.settings.seo.robots') }}" method="POST"
            class="space-y-4 xl:space-y-6 form-submit-add">
            @csrf
            <div class="editorContainer" data-input="robot_text"></div>
            <input type="hidden" name="robot_text" id="robot_text" value="{{ getOption('robot_text') }}">

            <div class="flex items-center justify-end mt-4">
                <x-admin::primary-button type="submit">
                    {{ __('Save') }}
                </x-admin::primary-button>
            </div>
        </form>
    </div>
    @push('scripts')
        @vite('resources/admin/js/settings/robots.js')
    @endpush
</x-admin-app-layout>
