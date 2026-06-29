<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Logo and Favicon') }}" :dateFilter="false" />
        <div class="flex items-start gap-3 rounded-md border border-warning/40 px-4 py-3 bg-warning/5 mb-6">
            <i class="ph ph-lightbulb text-warning text-lg"></i>
            <p class="text-warning s-text">
                {{ __('If the logo and favicon are not changed after you update from this page, please clear the cache from your browser. As we keep the filename the same after the update, it may show the old image for the cache. usually, it works after clear the cache but if you still see the old logo or favicon, it may be caused by server level or network level caching. Please clear them too.') }}
            </p>
        </div>
        <form action="{{ route('admin.settings.logo-favicon.store') }}" method="POST"
            class="grid grid-cols-2 gap-4 xl:gap-6 form-submit-edit">
            @csrf
            <div class="col-span-2 md:col-span-1">
                <p class="m-text mb-4">{{ __('Logo For Light Mode') }}</p>
                <div
                    class="relative border border-neutral-30 dark:border-neutral-500 rounded-lg p-8 xl:py-12 flex justify-center mb-7">
                    <div id="logo_light_preview"> 
                        <img src="{{ config('application_info.logo_favicon.logo_light') ?? '/assets/logo.png' }}"
                            alt="logo light" />
                    </div>
                    <input type="text" class="sr-only" id="logo_light_input" name="logo_light"
                        value="{{ config('application_info.logo_favicon.logo_light') ?? '/assets/logo.png' }}" />
                    <label for="logo_light" id="logo_light" data-input="logo_light_input"
                        data-preview="logo_light_preview"
                        class="absolute cursor-pointer size-10 xl:size-12 right-5 bottom-0 translate-y-1/2 bg-primary rounded-full text-neutral-0 text-2xl f-center">
                        <i class="ph ph-cloud-arrow-up"></i>
                    </label>
                </div>
                <div class="flex flex-col md:justify-between md:flex-row">
                    <p class="text-neutral-400 text-xs">{{ __('Supported Files') }}: JPG, Png</p>
                    <p class="text-neutral-400 text-xs mt-2 md:mt-0 md:text-right">{{ __('Size') }}: 140 x 42</p>
                </div>
            </div>
            <div class="col-span-2 md:col-span-1">
                <p class="m-text mb-4">{{ __('Logo For Dark Mode') }}</p>
                <div
                    class="relative border bg-neutral-904 border-neutral-30 dark:border-neutral-500 rounded-lg p-8 xl:py-12 flex justify-center mb-7">
                    <div id="logo_dark_preview">
                        <img src="{{ config('application_info.logo_favicon.logo_dark') ?? '/assets/logo.png' }}"
                            alt="logo dark" />
                    </div>
                    <input type="text" class="sr-only" id="logo_dark_input" name="logo_dark"
                        value="{{ config('application_info.logo_favicon.logo_dark') ?? '/assets/logo.png' }}" />
                    <label for="logo_dark" id="logo_dark" data-input="logo_dark_input" data-preview="logo_dark_preview"
                        class="absolute cursor-pointer size-10 xl:size-12 right-5 bottom-0 translate-y-1/2 bg-primary rounded-full text-neutral-0 text-2xl f-center">
                        <i class="ph ph-cloud-arrow-up"></i>
                    </label>
                </div>
                <div class="flex flex-col md:justify-between md:flex-row">
                    <p class="text-neutral-400 text-xs">{{ __('Supported Files') }}: JPG, Png</p>
                    <p class="text-neutral-400 text-xs mt-2 md:mt-0 md:text-right">{{ __('Size') }}: 140 x 42</p>
                </div>
            </div>
            <div class="col-span-2">
                <p class="m-text mb-4">{{ __('Favicon') }}</p>
                <div
                    class="relative border border-neutral-30 dark:border-neutral-500 rounded-lg p-8 xl:py-12 flex justify-center mb-7">
                    <div id="favicon_preview">
                        <img src="{{ config('application_info.logo_favicon.favicon') ?? '/favicon.ico' }}"
                            alt="favicon_preview" />
                    </div>
                    <input type="text" class="sr-only" id="favicon_input" name="favicon"
                        value="{{ config('application_info.logo_favicon.favicon') ?? '/favicon.ico' }}" />
                    <label for="logo" id="favicon" data-input="favicon_input" data-preview="favicon_preview"
                        class="absolute cursor-pointer size-10 xl:size-12 right-5 bottom-0 translate-y-1/2 bg-primary rounded-full text-neutral-0 text-2xl f-center">
                        <i class="ph ph-cloud-arrow-up"></i>
                    </label>
                </div>
                <div class="flex flex-col md:justify-between md:flex-row">
                    <p class="text-neutral-400 text-xs">{{ __('Supported Files') }}: JPG, Png</p>
                    <p class="text-neutral-400 text-xs mt-2 md:mt-0 md:text-right">{{ __('Size') }}: 32 x 32</p>
                </div>
            </div>
            <div class="col-span-2 flex gap-3">
                <x-admin::primary-button type="submit">
                    {{ __('Save') }}
                </x-admin::primary-button>
            </div>
        </form>
    </div>
    @push('scripts')
        @vite('resources/admin/js/settings/general.js')
    @endpush
</x-admin-app-layout>
