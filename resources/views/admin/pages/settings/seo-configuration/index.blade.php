<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('SEO Configuration') }}" :buttons="$buttons" :isFilterable="false" />
        <form action="{{ route('admin.settings.seo.update') }}" method="POST"
            class="space-y-4 xl:space-y-6 form-submit-edit">
            @csrf
            <x-admin::text-input-group value="{{ $seoSettings['title'] ?? '' }}" name="title" label="Title"
                placeholder="Quiz" />
            <x-admin::text-input-group value="{{ $seoSettings['author'] ?? '' }}" name="author" label="Author"
                placeholder="John Doe" />
            <x-admin::text-input-group value="{{ $seoSettings['canonical_link'] ?? '' }}" name="canonical_link"
                placeholder="https://example.com" label="Canonical Link" />
            <x-admin::text-input-group value="{{ $seoSettings['alternates']['canonical'] ?? '' }}"
                placeholder="https://example.com" name="alternates[canonical]" label="Alternate Canonical Link" />
            <div class="input-group">
                <x-admin::label for="keywords">
                    {{ __('Keywords') }}
                </x-admin::label>
                <x-admin::select-option class="tags-select-2" multiple="multiple" name="keywords[]" id="keywords">
                    @foreach (explode(',', $seoSettings['keywords'] ?? '') as $item)
                        <option value="{{ $item }}" @selected(true)>{{ $item }}
                        </option>
                    @endforeach
                </x-admin::select-option>
                <x-admin::input-error name="keywords" />
            </div>
            <x-admin::file-uploader type="text" name="image" class="file-uploader" :value="$seoSettings['image'] ?? ''"
                label="SEO Image" />
            <x-admin::textarea-group name="description" label="Description" :value="$seoSettings['description'] ?? ''" />
            <div class="white-box">
                <div class="flex justify-between items-center">
                    <p class="m-text font-medium">{{ __('Meta Tags') }}</p>
                    <button type="button" class="btn-primary" id="extra_field_container">
                        <i class="ph ph-plus"></i>
                        <span class="text-xs font-medium">{{ __('Add Meta Tag') }}</span>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 py-4 pt-8" id="row_container">
                    @foreach ($seoSettings['meta'] ?? [] as $index => $item)
                        <div class="space-y-4 border border-neutral-30 dark:border-neutral-500 p-3 rounded-lg relative"
                            id="meta-row-{{ $index }}">
                            <x-admin::text-input-group name="meta[{{ $index }}][name]" label="Meta Name"
                                value="{{ $item['name'] ?? '' }}" />
                            <x-admin::textarea-group name="meta[{{ $index }}][content]" label="Meta Content"
                                :value="$item['content'] ?? ''" />
                            <button type="button" class="text-red-500 cursor-pointer mt-2 delete-meta absolute right-2 -top-1 hover:text-red-900"
                                data-id="{{ $index }}">
                                <i class="ph ph-trash"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="white-box">
                <p class="m-text font-medium">{{ __('Twitter Metadata') }}</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 xl:gap-6">
                    <x-admin::text-input-group name="twitter[card]" label="Card" :value="$seoSettings['twitter']['card'] ?? ''" />
                    <x-admin::text-input-group name="twitter[site]" label="{{ __('Site') }} (@handle)"
                        :value="$seoSettings['twitter']['site'] ?? ''" />
                    <x-admin::text-input-group name="twitter[creator]" label="{{ __('Creator') }} (@handle)"
                        :value="$seoSettings['twitter']['creator'] ?? ''" />
                    <x-admin::text-input-group name="twitter[title]" label="Title" :value="$seoSettings['twitter']['title'] ?? ''" />
                </div>
                <x-admin::textarea-group name="twitter[description]" label="Description" :value="$seoSettings['twitter']['description'] ?? ''" />
                <x-admin::file-uploader type="text" name="twitter[image]" label="Image URL" :value="$seoSettings['twitter']['image'] ?? ''" />
            </div>
            <div class="white-box">
                <p class="m-text font-medium">{{ __('Open Graph Metadata') }}</p>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 xl:gap-6">
                    <x-admin::text-input-group name="openGraph[title]" label="{{ __('OG Title') }}"
                        :value="$seoSettings['openGraph']['title'] ?? ''" />
                    <x-admin::text-input-group name="openGraph[type]" label="{{ __('OG Type') }}" :value="$seoSettings['openGraph']['type'] ?? ''" />
                    <x-admin::text-input-group name="openGraph[url]" label="{{ __('OG URL') }}" :value="$seoSettings['openGraph']['url'] ?? ''" />
                    <x-admin::text-input-group name="openGraph[site_name]" label="{{ __('OG Site Name') }}"
                        :value="$seoSettings['openGraph']['site_name'] ?? ''" />
                    <x-admin::text-input-group name="openGraph[locale]" label="{{ __('OG Locale') }}"
                        :value="$seoSettings['openGraph']['locale'] ?? ''" />
                    <x-admin::text-input-group name="openGraph[imageAlt]" label="{{ __('OG Image Alt') }}"
                        :value="$seoSettings['openGraph']['imageAlt'] ?? ''" />
                    <x-admin::text-input-group name="openGraph[imageWidth]" label="{{ __('OG Image Width') }}"
                        :value="$seoSettings['openGraph']['imageWidth'] ?? ''" />
                    <x-admin::text-input-group name="openGraph[imageHeight]" label="{{ __('OG Image Height') }}"
                        :value="$seoSettings['openGraph']['imageHeight'] ?? ''" />
                </div>
                <div class="mt-4 space-y-4">
                    <x-admin::textarea-group name="openGraph[description]" label="{{ __('OG Description') }}"
                        :value="$seoSettings['openGraph']['description'] ?? ''" />
                    <x-admin::file-uploader type="text" name="openGraph[image]" label="{{ __('OG Image') }}"
                        :value="$seoSettings['openGraph']['image'] ?? ''" />
                </div>
            </div>
            <div class="white-box">
                <p class="m-text font-medium">{{ __('Favicon') }}</p>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 py-4 pt-8">
                    @foreach ($seoSettings['favicon'] ?? [] as $index => $favicon)
                        <div class="space-y-4 border border-neutral-30 dark:border-neutral-500 p-3 rounded-lg">
                            <x-admin::text-input-group type='hidden' name="favicon[{{ $index }}][rel]"
                                value="{{ $favicon['rel'] ?? '' }}" />
                            <x-admin::text-input-group type='hidden' name="favicon[{{ $index }}][type]"
                                value="{{ $favicon['type'] ?? '' }}" />
                            <x-admin::text-input-group type='hidden' name="favicon[{{ $index }}][sizes]"
                                value="{{ $favicon['sizes'] ?? '' }}" />
                            <x-admin::text-input-group label="Rel" disabled="true"
                                value="{{ $favicon['rel'] ?? '' }}" />
                            <x-admin::text-input-group label="Type" disabled="true"
                                value="{{ $favicon['type'] ?? '' }}" />
                            <x-admin::text-input-group label="Sizes" disabled="true"
                                value="{{ $favicon['sizes'] ?? '' }}" />
                            <x-admin::file-uploader type="text" name="favicon[{{ $index }}][href]"
                                class="file-uploader" label="Href (Icon Path)" :value="$favicon['href'] ?? ''" />
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="white-box">
                <p class="m-text font-medium">{{ __('Structured Data (JSON-LD)') }}</p>
                <div class="editorContainer" data-input="structuredDataInput"></div>
                <input type="hidden" name="structured_data[script][content]" id="structuredDataInput"
                    value="{{ $seoSettings['structured_data']['script']['content'] ?? '' }}">
            </div>
            <div class="flex items-center justify-end mt-4">
                <x-admin::primary-button type="submit">
                    {{ __('Save') }}
                </x-admin::primary-button>
            </div>
        </form>
    </div>

    @push('scripts')
        @vite('resources/admin/js/settings/seo.js')
    @endpush
</x-admin-app-layout>
