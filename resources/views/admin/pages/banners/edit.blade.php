<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Edit Banner') }}" :buttons="$buttons" :isFilterable="false" />
        <form action="{{ route('admin.banners.update', $banner->id) }}" class="form-submit-edit" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 gap-4 xl:gap-6">
                <x-admin::text-input-group name="title" label="Title" :value="$banner->title" placeholder="{{ __('e.g. New Collection 2026') }}" />

                <x-admin::text-input-group name="subtitle" label="Subtitle" :value="$banner->subtitle" placeholder="{{ __('e.g. Discover the latest arrivals') }}" />

                <div class="input-group">
                    <x-admin::label for="image">{{ __('Banner Image') }}</x-admin::label>
                    <x-admin::file-uploader name="image" id="image" :value="$banner->image" />
                </div>

                <x-admin::text-input-group name="button_text" label="Button Text" :value="$banner->button_text" placeholder="{{ __('e.g. Shop Now') }}" />

                <x-admin::text-input-group name="link" label="Link URL" :value="$banner->link" placeholder="{{ __('e.g. /shop') }}" />

                <x-admin::number-input-group name="sort_order" label="Sort Order" :value="$banner->sort_order" placeholder="0" :with_currencySymbol="false" />

                <div class="input-group">
                    <x-admin::label for="is_active">{{ __('Active') }}</x-admin::label>
                    <x-admin::switch name="is_active" id="is_active" :value="(int) $banner->is_active" :types="[['label' => __('Inactive'), 'value' => 0], ['label' => __('Active'), 'value' => 1]]" />
                </div>
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-admin::primary-button type="submit">
                    {{ __('Save') }}
                </x-admin::primary-button>
            </div>
        </form>
    </div>
</x-admin-app-layout>
