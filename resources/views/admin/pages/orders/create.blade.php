<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Create Order') }}" :buttons="$buttons" :isFilterable="false" />

        <p class="text-sm text-neutral-500 mb-6">
            {{ __('Manually enter an order taken over WhatsApp, phone, or in person. Stock is reserved just like a storefront checkout. Payment is Cash on Delivery.') }}
        </p>

        <form action="{{ route('admin.orders.store') }}" class="form-submit-edit" method="POST">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Left: products --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="border-b border-neutral-200 dark:border-neutral-700 pb-2">
                        <h3 class="text-sm font-semibold text-neutral-500 uppercase tracking-wider">{{ __('Products') }}</h3>
                    </div>

                    {{-- Product search --}}
                    <div class="relative">
                        <x-admin::label for="product-search">{{ __('Search & add products') }}</x-admin::label>
                        <input type="text" id="product-search" autocomplete="off"
                            class="text-input" placeholder="{{ __('Type a product name or SKU…') }}" />
                        <div id="product-search-results"
                            class="absolute z-20 left-0 right-0 mt-1 bg-neutral-0 dark:bg-neutral-900 border border-neutral-30 dark:border-neutral-700 rounded-lg shadow-lg max-h-72 overflow-y-auto hidden"></div>
                    </div>

                    {{-- Selected line items --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-neutral-500 border-b border-neutral-200 dark:border-neutral-700">
                                    <th class="py-2 pr-2">{{ __('Product') }}</th>
                                    <th class="py-2 px-2 w-28">{{ __('Price') }}</th>
                                    <th class="py-2 px-2 w-28">{{ __('Qty') }}</th>
                                    <th class="py-2 px-2 w-32 text-right">{{ __('Subtotal') }}</th>
                                    <th class="py-2 pl-2 w-10"></th>
                                </tr>
                            </thead>
                            <tbody id="order-items-body">
                                {{-- rows injected by JS --}}
                            </tbody>
                        </table>
                        <p id="no-items-row" class="text-sm text-neutral-400 py-6 text-center">
                            {{ __('No products added yet. Search above to add items.') }}
                        </p>
                    </div>
                </div>

                {{-- Right: customer + summary --}}
                <div class="space-y-6">
                    <div class="border-b border-neutral-200 dark:border-neutral-700 pb-2">
                        <h3 class="text-sm font-semibold text-neutral-500 uppercase tracking-wider">{{ __('Customer') }}</h3>
                    </div>

                    <x-admin::text-input-group name="customer_name" label="{{ __('Customer Name') }}" :required="true" />
                    <x-admin::text-input-group name="customer_phone" label="{{ __('Phone') }}" :required="true" />
                    <x-admin::text-input-group name="customer_email" label="{{ __('Email') }}" type="email" />
                    <x-admin::textarea-group name="shipping_address" label="{{ __('Shipping Address') }}" :required="true" />

                    <div class="grid grid-cols-2 gap-4">
                        <x-admin::text-input-group name="city" label="{{ __('City') }}" />
                        <x-admin::text-input-group name="zip_code" label="{{ __('Zip Code') }}" />
                    </div>

                    <x-admin::number-input-group name="shipping_cost" label="{{ __('Shipping Cost') }}"
                        :value="0" :with_currencySymbol="false" />

                    <x-admin::textarea-group name="note" label="{{ __('Order Note') }}" />

                    {{-- Totals --}}
                    <div class="bg-neutral-10 dark:bg-neutral-900 rounded-lg p-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-neutral-500">{{ __('Subtotal') }}</span>
                            <span id="summary-subtotal">{{ amountWithSymbol(0) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-neutral-500">{{ __('Shipping') }}</span>
                            <span id="summary-shipping">{{ amountWithSymbol(0) }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-base border-t border-neutral-200 dark:border-neutral-700 pt-2">
                            <span>{{ __('Total') }}</span>
                            <span id="summary-total">{{ amountWithSymbol(0) }}</span>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary w-full">{{ __('Create Order') }}</button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            (function () {
                const searchUrl = @json(route('admin.orders.product-search'));
                const currency = @json(currencySymbol());
                const searchInput = document.getElementById('product-search');
                const resultsBox = document.getElementById('product-search-results');
                const itemsBody = document.getElementById('order-items-body');
                const noItemsRow = document.getElementById('no-items-row');
                const shippingInput = document.querySelector('input[name="shipping_cost"]');

                let rowIndex = 0;
                let searchTimer = null;

                const money = (n) => currency + Number(n || 0).toLocaleString(undefined, { maximumFractionDigits: 2 });
                const rowKey = (pid, vid) => pid + ':' + (vid || '0');

                function recalc() {
                    let subtotal = 0;
                    itemsBody.querySelectorAll('tr').forEach(tr => {
                        const price = parseFloat(tr.dataset.price);
                        const qty = parseInt(tr.querySelector('.qty-input').value) || 0;
                        const line = price * qty;
                        tr.querySelector('.line-subtotal').textContent = money(line);
                        subtotal += line;
                    });
                    const shipping = parseFloat(shippingInput?.value) || 0;
                    document.getElementById('summary-subtotal').textContent = money(subtotal);
                    document.getElementById('summary-shipping').textContent = money(shipping);
                    document.getElementById('summary-total').textContent = money(subtotal + shipping);
                    noItemsRow.classList.toggle('hidden', itemsBody.children.length > 0);
                }

                function addRow(product, variant) {
                    const pid = product.id;
                    const vid = variant ? variant.id : '';
                    const key = rowKey(pid, vid);

                    // If the same product/variant is already added, just bump its qty.
                    const existing = itemsBody.querySelector(`tr[data-key="${key}"]`);
                    if (existing) {
                        const q = existing.querySelector('.qty-input');
                        q.value = (parseInt(q.value) || 0) + 1;
                        recalc();
                        return;
                    }

                    const price = variant ? variant.price : product.price;
                    const stock = variant ? variant.stock : product.stock;
                    const label = product.name + (variant ? ' — ' + variant.name : '');
                    const i = rowIndex++;

                    const tr = document.createElement('tr');
                    tr.className = 'border-b border-neutral-100 dark:border-neutral-800';
                    tr.dataset.key = key;
                    tr.dataset.price = price;
                    tr.innerHTML = `
                        <td class="py-2 pr-2">
                            ${escapeHtml(label)}
                            <span class="block text-xs text-neutral-400">${currency}${price} · ${stock} ${@json(__('in stock'))}</span>
                            <input type="hidden" name="items[${i}][product_id]" value="${pid}">
                            <input type="hidden" name="items[${i}][variant_id]" value="${vid}">
                        </td>
                        <td class="py-2 px-2">${money(price)}</td>
                        <td class="py-2 px-2">
                            <input type="number" min="1" max="${stock}" value="1"
                                name="items[${i}][quantity]"
                                class="qty-input text-input !py-1 !px-2 w-20">
                        </td>
                        <td class="py-2 px-2 text-right line-subtotal">${money(price)}</td>
                        <td class="py-2 pl-2 text-right">
                            <button type="button" class="remove-row text-error hover:opacity-70"><i class="ph ph-trash text-lg"></i></button>
                        </td>`;
                    itemsBody.appendChild(tr);

                    tr.querySelector('.qty-input').addEventListener('input', recalc);
                    tr.querySelector('.remove-row').addEventListener('click', () => { tr.remove(); recalc(); });

                    recalc();
                }

                function escapeHtml(s) {
                    const d = document.createElement('div');
                    d.textContent = s;
                    return d.innerHTML;
                }

                function renderResults(items) {
                    if (!items.length) {
                        resultsBox.innerHTML = `<div class="px-4 py-3 text-sm text-neutral-400">${@json(__('No products found.'))}</div>`;
                        resultsBox.classList.remove('hidden');
                        return;
                    }
                    resultsBox.innerHTML = '';
                    items.forEach(p => {
                        if (p.has_variants) {
                            p.variants.forEach(v => {
                                resultsBox.appendChild(resultItem(p, v));
                            });
                        } else {
                            resultsBox.appendChild(resultItem(p, null));
                        }
                    });
                    resultsBox.classList.remove('hidden');
                }

                function resultItem(product, variant) {
                    const price = variant ? variant.price : product.price;
                    const stock = variant ? variant.stock : product.stock;
                    const label = product.name + (variant ? ' — ' + variant.name : '');
                    const el = document.createElement('button');
                    el.type = 'button';
                    el.className = 'w-full text-left px-4 py-2 hover:bg-neutral-20 dark:hover:bg-neutral-800 flex justify-between items-center text-sm';
                    el.innerHTML = `<span>${escapeHtml(label)}</span><span class="text-neutral-400">${currency}${price} · ${stock}</span>`;
                    el.disabled = stock < 1;
                    if (stock < 1) el.classList.add('opacity-40', 'cursor-not-allowed');
                    el.addEventListener('click', () => {
                        addRow(product, variant);
                        resultsBox.classList.add('hidden');
                        searchInput.value = '';
                    });
                    return el;
                }

                searchInput.addEventListener('input', function () {
                    clearTimeout(searchTimer);
                    const q = this.value.trim();
                    searchTimer = setTimeout(() => {
                        fetch(searchUrl + '?q=' + encodeURIComponent(q), {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        })
                            .then(r => r.json())
                            .then(j => renderResults(j.data || []))
                            .catch(() => {});
                    }, 250);
                });

                document.addEventListener('click', (e) => {
                    if (!resultsBox.contains(e.target) && e.target !== searchInput) {
                        resultsBox.classList.add('hidden');
                    }
                });

                shippingInput?.addEventListener('input', recalc);
            })();
        </script>
    @endpush
</x-admin-app-layout>
