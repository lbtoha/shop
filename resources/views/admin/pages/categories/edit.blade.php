<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Edit Category') }}" :buttons="$buttons" :isFilterable="false" />
        <form action="{{ route('admin.categories.update', $category->id) }}" class="form-submit-edit" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 gap-4 xl:gap-6">
                <x-admin::text-input-group name="name" label="Name" placeholder="Enter Category Name" :value="$category->name" :required="true" />

                <div class="input-group">
                    <x-admin::label for="parent_id">{{ __('Parent Category') }}</x-admin::label>
                    <x-admin::select-option id="parent_id" name="parent_id" placeholder="{{ __('Select Parent Category') }}">
                        <option value="">{{ __('None') }}</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (int) $category->parent_id === (int) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </x-admin::select-option>
                </div>

                <x-admin::editor name="description" label="{{ __('Description') }}" :value="$category->description ?? ''" />

                <div class="input-group">
                    <x-admin::label for="image">{{ __('Image') }}</x-admin::label>
                    <x-admin::file-uploader name="image" id="image" :value="$category->image ?? null" />
                </div>

                <x-admin::number-input-group name="sort_order" label="Sort Order" :value="$category->sort_order ?? 0" placeholder="0" :with_currencySymbol="false" />

                <div class="input-group">
                    <x-admin::label for="is_active">{{ __('Active') }}</x-admin::label>
                    <x-admin::switch name="is_active" id="is_active" :value="(int) ($category->is_active ?? 0)" :types="[['label' => __('Inactive'), 'value' => 0], ['label' => __('Active'), 'value' => 1]]" />
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
