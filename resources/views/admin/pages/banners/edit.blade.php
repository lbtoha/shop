<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Edit Banner') }}" :buttons="$buttons" :isFilterable="false" />
        <form action="{{ route('admin.banners.update', $banner->id) }}" class="form-submit-edit" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 gap-4 xl:gap-6">
                <div class="input-group">
                    <x-admin::label for="image">{{ __('Banner Image') }}</x-admin::label>
                    <x-admin::file-uploader name="image" id="image" :value="$banner->image" />
                </div>

                <div class="input-group">
                    <x-admin::label for="category_id">{{ __('Link to Category') }}</x-admin::label>
                    <x-admin::select-option id="category_id" name="category_id" placeholder="{{ __('Select a Category') }}">
                        <option value="">{{ __('None (Use Custom Link URL below)') }}</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" @selected($cat->id == $banner->category_id)>{{ $cat->name }}</option>
                        @endforeach
                    </x-admin::select-option>
                </div>

                <x-admin::text-input-group name="link" label="Custom Link URL" :value="$banner->link" placeholder="{{ __('e.g. /shop') }}" />

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
