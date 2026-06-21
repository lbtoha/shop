@php
    $isEdit = isset($homeSection) && $homeSection;
    $action = $isEdit ? route('admin.home-sections.update', $homeSection->id) : route('admin.home-sections.store');
    $currentSource = old('source', $isEdit ? $homeSection->source->value : 'category');
    $currentLayout = old('layout', $isEdit ? $homeSection->layout->value : 'grid');
    $currentCategory = old('category_id', $isEdit ? $homeSection->category_id : null);

    // Chips to render: submitted order on validation failure, else the saved selection.
    $productsById = $products->keyBy('id');
    $chipProducts = collect(old('product_ids', $selectedProducts->pluck('id')->all()))
        ->map(fn ($id) => $productsById->get($id))
        ->filter()
        ->values();
@endphp

<form action="{{ $action }}" class="form-submit-edit home-section-form" method="POST">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 xl:gap-6">
        <x-admin::text-input-group name="title" label="Heading"
            :value="old('title', $homeSection->title ?? '')"
            placeholder="{{ __('e.g. Boys\' Collection (leave blank to auto-use category name)') }}" />

        <x-admin::text-input-group name="subtitle" label="Eyebrow Label"
            :value="old('subtitle', $homeSection->subtitle ?? '')"
            placeholder="{{ __('e.g. New In') }}" />

        <div class="input-group">
            <x-admin::label for="source">{{ __('Content Source') }}</x-admin::label>
            <x-admin::select-option name="source" id="source" class="text-input">
                @foreach ($sourceOptions as $option)
                    <option value="{{ $option['value'] }}" @selected($currentSource === $option['value'])>
                        {{ __($option['label']) }}
                    </option>
                @endforeach
            </x-admin::select-option>
            <x-admin::input-error name="source" />
        </div>

        <div class="input-group">
            <x-admin::label for="layout">{{ __('Layout') }}</x-admin::label>
            <x-admin::select-option name="layout" id="layout" class="text-input">
                @foreach ($layoutOptions as $option)
                    <option value="{{ $option['value'] }}" @selected($currentLayout === $option['value'])>
                        {{ __($option['label']) }}
                    </option>
                @endforeach
            </x-admin::select-option>
            <x-admin::input-error name="layout" />
        </div>

        {{-- Category: the section's category (source = category) OR an optional
             filter limiting a custom list to one category (source = products). --}}
        <div class="input-group" data-source-field="category,products">
            <x-admin::label for="category_id">{{ __('Category') }}</x-admin::label>
            <x-admin::select-option name="category_id" id="category_id" placeholder="{{ __('Select a category') }}">
                <option value="">{{ __('Select a category') }}</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) $currentCategory === (string) $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </x-admin::select-option>
            <p class="text-xs text-gray-400 mt-2" data-source-hint="products" hidden>
                {{ __('Optional. Filter the product list below by category to easily find products from different categories.') }}
            </p>
            <x-admin::input-error name="category_id" />
        </div>

        {{-- Custom product picker + draggable selection (source = products) --}}
        <div class="input-group md:col-span-2" data-source-field="products">
            <x-admin::label for="product_picker">{{ __('Products') }}</x-admin::label>

            {{-- Search & add (grouped by category for quick filtering). Not submitted itself. --}}
            <select id="product_picker" class="select-2" data-placeholder="{{ __('Search products to add…') }}">
                <option value="">{{ __('Search products to add…') }}</option>
                @foreach ($products->groupBy(fn ($p) => $p->category->name ?? __('Uncategorized')) as $catName => $items)
                    <optgroup label="{{ $catName }}">
                        @foreach ($items as $product)
                            <option value="{{ $product->id }}" data-name="{{ e($product->name) }}" data-category="{{ $product->category_id }}">{{ $product->name }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>

            <p class="text-xs text-gray-400 mt-2">
                <i class="ph ph-arrows-out-cardinal"></i>
                {{ __('Drag to reorder. Products display in this order. Inactive / out-of-stock items are skipped automatically.') }}
            </p>

            {{-- Selected products — each row carries the submitted product_ids[] value, reordered by drag. --}}
            <ul id="selected-products" class="mt-3 flex flex-col gap-2 max-w-2xl">
                @foreach ($chipProducts as $sp)
                    <li class="selected-product-item flex items-center gap-3 p-2.5 rounded-lg border border-neutral-30 dark:border-neutral-500 bg-neutral-0 dark:bg-neutral-904"
                        data-id="{{ $sp->id }}" data-category="{{ $sp->category_id }}">
                        <span class="product-drag-handle cursor-grab text-gray-400 hover:text-primary shrink-0">
                            <i class="ph ph-dots-six-vertical text-lg"></i>
                        </span>
                        <span class="flex-1 s-text truncate">{{ $sp->name }}</span>
                        <button type="button" class="remove-product text-gray-400 hover:text-danger shrink-0" title="{{ __('Remove') }}">
                            <i class="ph ph-x-circle text-lg"></i>
                        </button>
                        <input type="hidden" name="product_ids[]" value="{{ $sp->id }}">
                    </li>
                @endforeach
            </ul>

            <x-admin::input-error name="product_ids" />
        </div>

        {{-- Fallback behaviour when the selection is empty (source = products) --}}
        <div class="input-group" data-source-field="products">
            <x-admin::label for="fallback_latest">{{ __('When no products are selected') }}</x-admin::label>
            <x-admin::switch name="fallback_latest" id="fallback_latest"
                :value="(int) old('fallback_latest', $isEdit ? (int) $homeSection->fallback_latest : 0)"
                :types="[['label' => __('Hide section'), 'value' => 0], ['label' => __('Show latest products'), 'value' => 1]]" />
        </div>

        <x-admin::number-input-group name="product_limit" label="Product Limit"
            :value="old('product_limit', $homeSection->product_limit ?? 8)"
            placeholder="8" :with_currencySymbol="false" />

        <x-admin::text-input-group name="view_all_url" label="View All URL (optional)"
            :value="old('view_all_url', $homeSection->view_all_url ?? '')"
            placeholder="{{ __('Leave blank for an automatic link') }}" />

        <x-admin::number-input-group name="sort_order" label="Sort Order"
            :value="old('sort_order', $homeSection->sort_order ?? 0)"
            placeholder="0" :with_currencySymbol="false" />

        <div class="input-group">
            <x-admin::label for="is_active">{{ __('Visible') }}</x-admin::label>
            <x-admin::switch name="is_active" id="is_active"
                :value="(int) old('is_active', $homeSection->is_active ?? 1)"
                :types="[['label' => __('Hidden'), 'value' => 0], ['label' => __('Visible'), 'value' => 1]]" />
        </div>
    </div>

    <div class="flex items-center justify-end mt-4">
        <x-admin::primary-button type="submit">
            {{ __('Save') }}
        </x-admin::primary-button>
    </div>
</form>

@push('scripts')
    @vite('resources/admin/js/home-section/form.js')
@endpush
