<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Edit Product') }}" :buttons="$buttons" :isFilterable="false" />
        <form action="{{ route('admin.products.update', $product->id) }}" class="form-submit-edit" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 xl:gap-6">
                <div class="md:col-span-2">
                    <x-admin::text-input-group name="name" label="{{ __('Name') }}" placeholder="{{ __('Enter Product Name') }}" :value="$product->name" :required="true" />
                </div>

                <div>
                    <x-admin::label :for="'category_id'">{{ __('Category') }}</x-admin::label>
                    <x-admin::select-option id="category_id" name="category_id" placeholder="{{ __('Select Category') }}">
                        <option value="">{{ __('Select Category') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ (int) $product->category_id === $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </x-admin::select-option>
                </div>

                <div>
                    <x-admin::text-input-group name="sku" label="{{ __('SKU') }}" placeholder="{{ __('Enter SKU') }}" :value="$product->sku" />
                </div>

                <div>
                    <x-admin::number-input-group name="price" label="{{ __('Price') }}" placeholder="0.00" :value="$product->price" />
                </div>

                <div>
                    <x-admin::number-input-group name="compare_at_price" label="{{ __('Compare At Price') }}" placeholder="0.00" :value="$product->compare_at_price" />
                </div>

                <div>
                    <x-admin::number-input-group name="stock" label="{{ __('Stock') }}" placeholder="0" :value="$product->stock" :with_currencySymbol="false" />
                </div>

                <div class="md:col-span-2">
                    <x-admin::textarea-group name="short_description" label="{{ __('Short Description') }}" placeholder="{{ __('Enter Short Description') }}" :value="$product->short_description" />
                </div>

                <div class="md:col-span-2">
                    <x-admin::label :for="'description'">{{ __('Description') }}</x-admin::label>
                    <x-admin::editor id="description-editor" name="description" :value="$product->description ?? ''" />
                </div>

                <div>
                    <x-admin::label :for="'thumbnail'">{{ __('Thumbnail') }}</x-admin::label>
                    <x-admin::file-uploader name="thumbnail" id="thumbnail" :value="$product->thumbnail" />
                </div>

                <div class="flex items-center gap-8">
                    <div>
                        <x-admin::label :for="'is_active'">{{ __('Active') }}</x-admin::label>
                        <x-admin::switch name="is_active" id="is_active" :value="$product->is_active ? 1 : 0"
                            :types="[['label' => __('Inactive'), 'value' => 0], ['label' => __('Active'), 'value' => 1]]" />
                    </div>
                    <div>
                        <x-admin::label :for="'is_featured'">{{ __('Featured') }}</x-admin::label>
                        <x-admin::switch name="is_featured" id="is_featured" :value="$product->is_featured ? 1 : 0"
                            :types="[['label' => __('No'), 'value' => 0], ['label' => __('Yes'), 'value' => 1]]" />
                    </div>
                </div>

                {{-- Gallery: fixed set of 4 image slots named images[], prefilled from existing images.
                     On update the controller deletes all existing images and recreates from these slots. --}}
                <div class="md:col-span-2">
                    <x-admin::label>{{ __('Gallery Images') }}</x-admin::label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @php $existingImages = $product->images->pluck('image')->values(); @endphp
                        @for ($i = 0; $i < 4; $i++)
                            <x-admin::file-uploader name="images[]" id="gallery-{{ $i }}" :value="$existingImages[$i] ?? null" />
                        @endfor
                    </div>
                </div>

                @include('admin.pages.products._variants')
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-admin::primary-button type="submit">
                    {{ __('Save') }}
                </x-admin::primary-button>
            </div>
        </form>
    </div>
</x-admin-app-layout>
