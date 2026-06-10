{{-- Repeatable variant rows. Each row submits variants[i][...]. Leave the whole
     section empty for a simple single-stock product. --}}
@php($existing = isset($product) ? $product->variants : collect())
<div class="md:col-span-2">
    <div class="flex items-center justify-between mb-2">
        <x-admin::label>{{ __('Variants (size / color)') }}</x-admin::label>
        <button type="button" id="add-variant-row" class="btn-primary outlined !py-1.5 !px-3 text-xs">
            <i class="ph ph-plus"></i> {{ __('Add Variant') }}
        </button>
    </div>
    <p class="text-xs text-neutral-400 mb-3">{{ __('Add rows to sell this product in options. Each option carries its own stock. Price adjustment is added to the base price (use negatives for discounts). Leave empty for a single-stock product.') }}</p>

    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[680px]" id="variants-table">
            <thead>
                <tr class="text-left text-xs text-neutral-400">
                    <th class="py-2 pr-2">{{ __('Color') }}</th>
                    <th class="py-2 pr-2">{{ __('Size') }}</th>
                    <th class="py-2 pr-2">{{ __('SKU') }}</th>
                    <th class="py-2 pr-2 w-32">{{ __('Price +/-') }}</th>
                    <th class="py-2 pr-2 w-24">{{ __('Stock') }}</th>
                    <th class="py-2 w-10"></th>
                </tr>
            </thead>
            <tbody id="variants-body">
                @forelse ($existing as $i => $v)
                    <tr data-variant-row>
                        <td class="py-1 pr-2"><input type="text" name="variants[{{ $i }}][color]" value="{{ $v->attributes['Color'] ?? '' }}" placeholder="Red" class="w-full rounded-md border border-neutral-30 dark:border-neutral-600 bg-transparent px-2 py-1.5"></td>
                        <td class="py-1 pr-2"><input type="text" name="variants[{{ $i }}][size]" value="{{ $v->attributes['Size'] ?? '' }}" placeholder="L" class="w-full rounded-md border border-neutral-30 dark:border-neutral-600 bg-transparent px-2 py-1.5"></td>
                        <td class="py-1 pr-2"><input type="text" name="variants[{{ $i }}][sku]" value="{{ $v->sku }}" placeholder="SKU" class="w-full rounded-md border border-neutral-30 dark:border-neutral-600 bg-transparent px-2 py-1.5"></td>
                        <td class="py-1 pr-2"><input type="number" step="0.01" name="variants[{{ $i }}][price_adjustment]" value="{{ (float) $v->price_adjustment }}" class="w-full rounded-md border border-neutral-30 dark:border-neutral-600 bg-transparent px-2 py-1.5"></td>
                        <td class="py-1 pr-2"><input type="number" name="variants[{{ $i }}][stock]" value="{{ $v->stock }}" min="0" class="w-full rounded-md border border-neutral-30 dark:border-neutral-600 bg-transparent px-2 py-1.5"></td>
                        <td class="py-1 text-center"><button type="button" data-remove-variant class="text-red-500 hover:text-red-600"><i class="ph ph-trash"></i></button></td>
                    </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Row template (cloned by JS). Uses __INDEX__ placeholder. --}}
<template id="variant-row-template">
    <tr data-variant-row>
        <td class="py-1 pr-2"><input type="text" name="variants[__INDEX__][color]" placeholder="Red" class="w-full rounded-md border border-neutral-30 dark:border-neutral-600 bg-transparent px-2 py-1.5"></td>
        <td class="py-1 pr-2"><input type="text" name="variants[__INDEX__][size]" placeholder="L" class="w-full rounded-md border border-neutral-30 dark:border-neutral-600 bg-transparent px-2 py-1.5"></td>
        <td class="py-1 pr-2"><input type="text" name="variants[__INDEX__][sku]" placeholder="SKU" class="w-full rounded-md border border-neutral-30 dark:border-neutral-600 bg-transparent px-2 py-1.5"></td>
        <td class="py-1 pr-2"><input type="number" step="0.01" name="variants[__INDEX__][price_adjustment]" value="0" class="w-full rounded-md border border-neutral-30 dark:border-neutral-600 bg-transparent px-2 py-1.5"></td>
        <td class="py-1 pr-2"><input type="number" name="variants[__INDEX__][stock]" value="0" min="0" class="w-full rounded-md border border-neutral-30 dark:border-neutral-600 bg-transparent px-2 py-1.5"></td>
        <td class="py-1 text-center"><button type="button" data-remove-variant class="text-red-500 hover:text-red-600"><i class="ph ph-trash"></i></button></td>
    </tr>
</template>

@push('scripts')
<script>
    (function () {
        const body = document.getElementById('variants-body');
        const tpl = document.getElementById('variant-row-template');
        const addBtn = document.getElementById('add-variant-row');
        if (!body || !tpl || !addBtn) return;

        // Continue indexing after any server-rendered rows.
        let index = body.querySelectorAll('[data-variant-row]').length;

        addBtn.addEventListener('click', function () {
            const html = tpl.innerHTML.replace(/__INDEX__/g, index++);
            const tmp = document.createElement('tbody');
            tmp.innerHTML = html.trim();
            body.appendChild(tmp.firstElementChild);
        });

        body.addEventListener('click', function (e) {
            const btn = e.target.closest('[data-remove-variant]');
            if (!btn) return;
            btn.closest('[data-variant-row]')?.remove();
        });
    })();
</script>
@endpush
