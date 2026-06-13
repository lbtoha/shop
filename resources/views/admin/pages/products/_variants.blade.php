@php
    $existing = isset($product) ? $product->variants : collect();
    $existingAttrs = [];
    foreach ($existing as $v) {
        foreach ($v->attributes ?? [] as $k => $val) {
            if (!in_array($k, $existingAttrs, true)) {
                $existingAttrs[] = $k;
            }
        }
    }
    if (empty($existingAttrs) && $existing->isNotEmpty()) {
        $existingAttrs = ['Size'];
    } elseif (empty($existingAttrs)) {
        $existingAttrs = ['Color', 'Size'];
    }
    $existingAttrsStr = implode(', ', $existingAttrs);
@endphp

<div class="md:col-span-2" id="variants-container" data-initial-variants='@json($existing)'>
    <div class="flex flex-col gap-4 mb-4">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <x-admin::label class="!mb-0">{{ __('Product Variation Setup') }}</x-admin::label>
            
            <div class="flex items-center gap-3 flex-wrap">
                {{-- Quick Templates Dropdown --}}
                <select id="preset-selector" class="rounded-xl border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200 px-3 py-2 text-xs font-semibold focus:outline-none cursor-pointer">
                    <option value="">-- {{ __('Quick Templates') }} --</option>
                    <option value="womens-ready-made">{{ __("Women's Ready-Made (Color, Size 36-44)") }}</option>
                    <option value="baby-kids">{{ __("Baby & Kids (Color, Age 0m-3y)") }}</option>
                    <option value="mens-shirts">{{ __("Men's / Boys Shirts (Color, Size S-XXL)") }}</option>
                    <option value="mens-pants">{{ __("Men's Pants (Color, Waist 28-36)") }}</option>
                    <option value="traditional">{{ __("Traditional / Lungi (Color, Length)") }}</option>
                    <option value="clear">{{ __("Reset / Simple Product (No Variants)") }}</option>
                </select>

                <button type="button" id="add-variant-row" class="btn-primary outlined !py-2 !px-4 text-xs font-bold rounded-xl flex items-center gap-1.5 shrink-0">
                    <i class="ph ph-plus"></i> {{ __('Add Row') }}
                </button>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center bg-neutral-50 dark:bg-neutral-900/30 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800">
            <div class="flex-1 w-full">
                <x-admin::label :for="'attributes-list'" class="!mb-1.5 text-xs text-neutral-500 font-bold uppercase tracking-wider">{{ __('Define Variation Attributes') }}</x-admin::label>
                <input type="text" id="attributes-list" class="w-full rounded-xl border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200 px-3.5 py-2 text-sm focus:border-brand focus:outline-none transition-colors"
                    value="{{ $existingAttrsStr }}" placeholder="e.g. Color, Size, Length, Age">
                <p class="text-[10px] text-neutral-400 mt-1.5 font-medium">{{ __('Type attributes separated by commas. Columns will update automatically.') }}</p>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto border border-neutral-100 dark:border-neutral-800 rounded-2xl">
        <table class="w-full text-sm min-w-[720px]" id="variants-table">
            <thead>
                <tr class="text-left text-xs font-extrabold text-neutral-400 uppercase tracking-widest border-b border-neutral-100 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-900/10">
                    {{-- Dynamically generated header columns --}}
                    <th id="header-row-placeholder" class="hidden"></th>
                </tr>
            </thead>
            <tbody id="variants-body">
                {{-- Dynamically generated body rows --}}
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const container = document.getElementById('variants-container');
        const body = document.getElementById('variants-body');
        const table = document.getElementById('variants-table');
        const addBtn = document.getElementById('add-variant-row');
        const presetSelector = document.getElementById('preset-selector');
        const attributesInput = document.getElementById('attributes-list');
        const mainStockInput = document.querySelector('input[name="stock"]');

        if (!body || !table || !addBtn || !attributesInput) return;

        // Load existing variants from backend JSON
        let initialVariants = [];
        try {
            initialVariants = JSON.parse(container.getAttribute('data-initial-variants') || '[]');
        } catch (e) {
            console.error('Failed to parse initial variants', e);
        }

        // Map initial variants to our runtime structure
        let variantsData = initialVariants.map(v => ({
            sku: v.sku || '',
            price_adjustment: parseFloat(v.price_adjustment) || 0,
            stock: parseInt(v.stock) || 0,
            attrs: v.attributes || {}
        }));

        const presets = {
            'womens-ready-made': {
                attributes: ['Color', 'Size'],
                defaultRows: [
                    { attrs: { 'Color': '', 'Size': '36' }, price_adjustment: 0, stock: 50 },
                    { attrs: { 'Color': '', 'Size': '38' }, price_adjustment: 0, stock: 50 },
                    { attrs: { 'Color': '', 'Size': '40' }, price_adjustment: 0, stock: 50 },
                    { attrs: { 'Color': '', 'Size': '42' }, price_adjustment: 0, stock: 50 },
                    { attrs: { 'Color': '', 'Size': '44' }, price_adjustment: 0, stock: 50 }
                ]
            },
            'baby-kids': {
                attributes: ['Color', 'Age'],
                defaultRows: [
                    { attrs: { 'Color': '', 'Age': '0-3 months' }, price_adjustment: 0, stock: 50 },
                    { attrs: { 'Color': '', 'Age': '3-6 months' }, price_adjustment: 0, stock: 50 },
                    { attrs: { 'Color': '', 'Age': '6-12 months' }, price_adjustment: 0, stock: 50 },
                    { attrs: { 'Color': '', 'Age': '1 year' }, price_adjustment: 0, stock: 50 },
                    { attrs: { 'Color': '', 'Age': '2 years' }, price_adjustment: 0, stock: 50 },
                    { attrs: { 'Color': '', 'Age': '3 years' }, price_adjustment: 0, stock: 50 }
                ]
            },
            'mens-shirts': {
                attributes: ['Color', 'Size'],
                defaultRows: [
                    { attrs: { 'Color': '', 'Size': 'S' }, price_adjustment: 0, stock: 50 },
                    { attrs: { 'Color': '', 'Size': 'M' }, price_adjustment: 0, stock: 50 },
                    { attrs: { 'Color': '', 'Size': 'L' }, price_adjustment: 0, stock: 50 },
                    { attrs: { 'Color': '', 'Size': 'XL' }, price_adjustment: 0, stock: 50 },
                    { attrs: { 'Color': '', 'Size': 'XXL' }, price_adjustment: 0, stock: 50 }
                ]
            },
            'mens-pants': {
                attributes: ['Color', 'Waist'],
                defaultRows: [
                    { attrs: { 'Color': '', 'Waist': '28' }, price_adjustment: 0, stock: 50 },
                    { attrs: { 'Color': '', 'Waist': '30' }, price_adjustment: 0, stock: 50 },
                    { attrs: { 'Color': '', 'Waist': '32' }, price_adjustment: 0, stock: 50 },
                    { attrs: { 'Color': '', 'Waist': '34' }, price_adjustment: 0, stock: 50 },
                    { attrs: { 'Color': '', 'Waist': '36' }, price_adjustment: 0, stock: 50 }
                ]
            },
            'traditional': {
                attributes: ['Color', 'Length'],
                defaultRows: [
                    { attrs: { 'Color': '', 'Length': '2.25 yards' }, price_adjustment: 0, stock: 50 },
                    { attrs: { 'Color': '', 'Length': '2.5 yards' }, price_adjustment: 0, stock: 50 }
                ]
            }
        };

        // Parse attributes from the input text field
        function getActiveAttributes() {
            return attributesInput.value
                .split(',')
                .map(s => s.trim())
                .filter(s => s !== '');
        }

        // Read all current input field values from the DOM so we don't lose typed state when re-rendering
        function scrapeCurrentDOMData() {
            const rows = body.querySelectorAll('[data-variant-row]');
            const scraped = [];
            rows.forEach(row => {
                const item = {
                    sku: row.querySelector('input[name*="[sku]"]')?.value || '',
                    price_adjustment: parseFloat(row.querySelector('input[name*="[price_adjustment]"]')?.value) || 0,
                    stock: parseInt(row.querySelector('input[name*="[stock]"]')?.value) || 0,
                    attrs: {}
                };

                // Read attribute inputs dynamically
                const attrInputs = row.querySelectorAll('input[name*="[attrs]"]');
                attrInputs.forEach(input => {
                    const match = input.name.match(/\[attrs\]\[([^\]]+)\]/);
                    if (match && match[1]) {
                        item.attrs[match[1]] = input.value;
                    }
                });

                scraped.push(item);
            });
            return scraped;
        }

        // Re-render the headers and all rows
        function renderTable(preserveData = true) {
            if (preserveData) {
                variantsData = scrapeCurrentDOMData();
            }

            const activeAttrs = getActiveAttributes();

            // 1. Rebuild Headers
            const thead = table.querySelector('thead');
            thead.innerHTML = '';
            const trHeader = document.createElement('tr');
            trHeader.className = 'text-left text-xs font-extrabold text-neutral-400 uppercase tracking-widest border-b border-neutral-100 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-900/10';

            activeAttrs.forEach(attrName => {
                const th = document.createElement('th');
                th.className = 'px-4 py-3.5';
                th.textContent = attrName;
                trHeader.appendChild(th);
            });

            // Standard columns
            const stdCols = [
                { label: 'SKU', class: 'w-[18%]' },
                { label: 'Price Adjustment (৳)', class: 'w-[15%]' },
                { label: 'Stock', class: 'w-[12%]' },
                { label: '', class: 'w-10 text-center' }
            ];

            stdCols.forEach(col => {
                const th = document.createElement('th');
                th.className = 'px-4 py-3.5 ' + col.class;
                th.textContent = col.label;
                trHeader.appendChild(th);
            });

            thead.appendChild(trHeader);

            // 2. Rebuild Rows
            body.innerHTML = '';
            variantsData.forEach((row, rowIndex) => {
                const tr = document.createElement('tr');
                tr.setAttribute('data-variant-row', '');
                tr.className = 'border-b border-neutral-100 dark:border-neutral-800/80 hover:bg-neutral-50/40 dark:hover:bg-neutral-900/5 transition-colors';

                // Attribute columns
                activeAttrs.forEach(attrName => {
                    const td = document.createElement('td');
                    td.className = 'px-4 py-2.5';
                    const val = row.attrs[attrName] || '';
                    td.innerHTML = `<input type="text" name="variants[${rowIndex}][attrs][${attrName}]" value="${escapeHtml(val)}" placeholder="${escapeHtml(attrName)}" class="w-full rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200 px-3 py-2 text-xs focus:border-brand focus:outline-none transition-colors">`;
                    tr.appendChild(td);
                });

                // SKU
                const tdSku = document.createElement('td');
                tdSku.className = 'px-4 py-2.5';
                tdSku.innerHTML = `<input type="text" name="variants[${rowIndex}][sku]" value="${escapeHtml(row.sku)}" placeholder="SKU" class="w-full rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200 px-3 py-2 text-xs focus:border-brand focus:outline-none transition-colors">`;
                tr.appendChild(tdSku);

                // Price adjustment
                const tdPrice = document.createElement('td');
                tdPrice.className = 'px-4 py-2.5';
                tdPrice.innerHTML = `<input type="number" step="0.01" name="variants[${rowIndex}][price_adjustment]" value="${row.price_adjustment}" class="w-full rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200 px-3 py-2 text-xs focus:border-brand focus:outline-none transition-colors">`;
                tr.appendChild(tdPrice);

                // Stock
                const tdStock = document.createElement('td');
                tdStock.className = 'px-4 py-2.5';
                tdStock.innerHTML = `<input type="number" name="variants[${rowIndex}][stock]" value="${row.stock}" min="0" class="w-full rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200 px-3 py-2 text-xs focus:border-brand focus:outline-none transition-colors">`;
                tr.appendChild(tdStock);

                // Delete Button
                const tdDel = document.createElement('td');
                tdDel.className = 'px-4 py-2.5 text-center';
                tdDel.innerHTML = `<button type="button" data-remove-variant class="text-neutral-400 hover:text-red-500 transition-colors p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-950/20"><i class="ph ph-trash text-base"></i></button>`;
                tr.appendChild(tdDel);

                body.appendChild(tr);
            });

            syncProductStockFromVariants();
        }

        function syncProductStockFromVariants() {
            if (!mainStockInput) return;

            const variantRows = body.querySelectorAll('[data-variant-row]');
            if (variantRows.length > 0) {
                mainStockInput.readOnly = true;
                mainStockInput.classList.add('bg-neutral-100', 'dark:bg-neutral-800', 'cursor-not-allowed', 'text-neutral-500');
                
                let totalStock = 0;
                variantRows.forEach(row => {
                    const stockInput = row.querySelector('input[name*="[stock]"]');
                    if (stockInput) {
                        totalStock += parseInt(stockInput.value) || 0;
                    }
                });
                mainStockInput.value = totalStock;
            } else {
                mainStockInput.readOnly = false;
                mainStockInput.classList.remove('bg-neutral-100', 'dark:bg-neutral-800', 'cursor-not-allowed', 'text-neutral-500');
            }
        }

        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Add blank row
        addBtn.addEventListener('click', function () {
            variantsData = scrapeCurrentDOMData();
            const activeAttrs = getActiveAttributes();
            const newRow = {
                sku: '',
                price_adjustment: 0,
                stock: 0,
                attrs: {}
            };
            activeAttrs.forEach(attr => {
                newRow.attrs[attr] = '';
            });
            variantsData.push(newRow);
            renderTable(false);
        });

        // Remove row
        body.addEventListener('click', function (e) {
            const btn = e.target.closest('[data-remove-variant]');
            if (!btn) return;
            btn.closest('[data-variant-row]')?.remove();
            syncProductStockFromVariants();
        });

        // Trigger render when attribute names input changes
        attributesInput.addEventListener('change', () => renderTable(true));

        // Preset selector listener
        presetSelector.addEventListener('change', function () {
            const val = this.value;
            if (val === 'clear') {
                variantsData = [];
                attributesInput.value = 'Color, Size';
                renderTable(false);
                presetSelector.value = '';
                return;
            }

            const config = presets[val];
            if (config) {
                attributesInput.value = config.attributes.join(', ');
                variantsData = JSON.parse(JSON.stringify(config.defaultRows)); // deep copy
                renderTable(false);
            }
            presetSelector.value = ''; // reset select
        });

        // Listen for stock changes in the variant rows
        body.addEventListener('input', function (e) {
            if (e.target.name && e.target.name.includes('[stock]')) {
                syncProductStockFromVariants();
            }
        });

        // Initial render on load
        renderTable(false);
    })();
</script>
@endpush
