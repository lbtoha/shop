<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('PWA Configuration') }}" :buttons="$buttons" :isFilterable="false" />
        <p class="lg-text font-medium text-red-500">
            {{ __("Note: Only work when you install our 'standealone-client.zip' file on your server") }}</p>
        <form action="{{ route('admin.settings.pwa.store') }}" method="POST" class="space-y-4 xl:space-y-6 form-submit-edit">
            @csrf

            <x-admin::text-input-group value="{{ $pwa['title'] ?? '' }}" name="title" label="Title" />

            <x-admin::textarea-group name="description" label="Description" :value="__($pwa['description']) ?? ''" />
            <div class="border border-neutral-30 dark:border-neutral-700 p-4 rounded">
                <h6 class="h6 mb-4">{{ __('Theme') }}</h6>
                <div class="grid grid-cols-2 gap-4 xl:gap-6">
                    <div class="col-span-2 md:col-span-1 ">
                        <x-admin::label for="background_color">
                            {{ __('Background Color') }}
                        </x-admin::label>
                        <div class="flex items-center gap-4">
                            <input name="background_color" type="color" value="{{ $pwa['background_color'] }}" />
                            <span>{{ $pwa['background_color'] }}</span>
                        </div>
                    </div>
                    <div class="col-span-2 md:col-span-1 ">
                        <x-admin::label for="theme_color">
                            {{ __('Theme Color') }}
                        </x-admin::label>
                        <div class="flex items-center gap-4">
                            <input name="theme_color" type="color" value="{{ $pwa['theme_color'] }}" />
                            <span>{{ $pwa['theme_color'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="white-box">
                <p class="lg-text font-medium">{{ __('Icons (Only PNG)') }}</p>
                <div class="grid grid-cols-2 gap-4 mt-2">
                    <div class="form-group">
                        <x-admin::label for="icons[0][src]" class="mb-2">Icons 1 (192x192)</x-admin::label>
                        <x-admin::file-uploader type="text" name="icons[0][src]" class="file-uploader"
                            :value="$pwa['icons'][0]['src'] ?? ''" />
                    </div>
                    <div class="form-group">
                        <x-admin::label for="icons[0][src]" class="mb-2">Icons 3 (512x512)</x-admin::label>
                        <x-admin::file-uploader type="text" name="icons[1][src]" class="file-uploader"
                            :value="$pwa['icons'][1]['src'] ?? ''" />
                    </div>

                </div>
            </div>
            <div class="white-box">
                <p class="lg-text font-medium">{{ __('Screenshots (Home Screen, Only PNG)') }}</p>
                <div class="grid grid-cols-2 gap-4 mt-2">
                    <div class="form-group">
                        <x-admin::label for="screenshots[0][src]" class="mb-2">Desktop (1366x768)</x-admin::label>
                        <x-admin::file-uploader type="text" name="screenshots[0][src]" class="file-uploader"
                            :value="$pwa['screenshots'][0]['src'] ?? ''" />
                    </div>
                    <div class="form-group">
                        <x-admin::label for="screenshots[0][src]" class="mb-2">Mobile (400x800)</x-admin::label>
                        <x-admin::file-uploader type="text" name="screenshots[1][src]" class="file-uploader"
                            :value="$pwa['screenshots'][1]['src'] ?? ''" />
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end mt-4 ">
                <x-admin::primary-button type="submit">
                    {{ __('Save') }}
                </x-admin::primary-button>
            </div>
        </form>
    </div>

    @push('scripts')
        @vite('resources/admin/js/settings/pwa.js')
    @endpush
</x-admin-app-layout>
