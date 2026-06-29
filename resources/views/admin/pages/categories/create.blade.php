<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Create New Category') }}" :buttons="$buttons" :isFilterable="false" />
        <form action="{{ route('admin.categories.store') }}" class="form-submit-edit" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-4 xl:gap-6">
                <x-admin::text-input-group name="name" label="Name" placeholder="Enter Category Name" :required="true" />

                <div class="input-group">
                    <x-admin::label for="parent_id">{{ __('Parent Category') }}</x-admin::label>
                    <x-admin::select-option id="parent_id" name="parent_id" placeholder="{{ __('Select Parent Category') }}">
                        <option value="">{{ __('None') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </x-admin::select-option>
                </div>

                <x-admin::editor name="description" label="{{ __('Description') }}" :value="''" />

                <div class="input-group">
                    <x-admin::label for="image">{{ __('Image') }}</x-admin::label>
                    <x-admin::file-uploader name="image" id="image" :value="null" />
                </div>

                <x-admin::number-input-group name="sort_order" label="Sort Order" :value="0" placeholder="0" :with_currencySymbol="false" />

                <div class="input-group">
                    <x-admin::label for="is_active">{{ __('Active') }}</x-admin::label>
                    <x-admin::switch name="is_active" id="is_active" :value="1" :types="[['label' => __('Inactive'), 'value' => 0], ['label' => __('Active'), 'value' => 1]]" />
                </div>

                <div class="input-group">
                    <x-admin::label for="show_in_slider">{{ __('Show in Homepage Slider') }}</x-admin::label>
                    <x-admin::select-option id="show_in_slider" name="show_in_slider">
                        <option value="2" selected>{{ __('Auto (Show only when it has products)') }}</option>
                        <option value="1">{{ __('Always Show (Show even if empty)') }}</option>
                        <option value="0">{{ __('Always Hide') }}</option>
                    </x-admin::select-option>
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
