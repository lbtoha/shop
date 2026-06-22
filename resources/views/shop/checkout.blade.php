@extends('shop.layouts.app')

@section('title', __('Checkout') . ' — ' . config('application_info.company_info.name'))

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-ink mb-6">{{ __('Checkout') }}</h1>

        <form method="POST" action="{{ route('shop.checkout.store') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @csrf

            {{-- Shipping / customer details & Items list --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Review Items Card --}}
                <div class="bg-white border border-[color:var(--color-line)] rounded-2xl p-6">
                    <h3 class="font-semibold text-ink mb-4">{{ __('Review Your Items') }}</h3>
                    <div class="divide-y divide-[color:var(--color-line)]">
                        @foreach ($items as $line)
                            @php($product = $line['product'])
                            @php($maxQty = $line['variant'] ? $line['variant']->stock : $product->stock)
                            <div class="py-4 flex items-center gap-4 checkout-item-row" 
                                data-line-key="{{ $line['key'] }}" 
                                data-unit-price="{{ $line['unit_price'] }}"
                                data-shipping-dhaka="{{ $product->shipping_cost_dhaka }}"
                                data-shipping-outside="{{ $product->shipping_cost_outside }}">
                                {{-- Image --}}
                                <a href="{{ route('shop.product', $product->slug) }}" target="_blank" class="w-16 h-20 shrink-0 rounded-lg bg-[color:var(--color-image)] overflow-hidden flex items-center justify-center border border-neutral-100">
                                    @if ($product->thumbnail)
                                        <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="ph ph-image text-xl text-neutral-300"></i>
                                    @endif
                                </a>

                                {{-- Details --}}
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-ink text-sm sm:text-base truncate hover:text-[color:var(--color-brand)]">
                                        <a href="{{ route('shop.product', $product->slug) }}" target="_blank">{{ $product->name }}</a>
                                    </h4>
                                    @if ($line['variant'])
                                        <div class="text-xs text-[color:var(--color-brand)] font-medium mt-0.5">Size : {{ $line['variant']->name }}</div>
                                    @endif
                                </div>

                                {{-- Price --}}
                                <div class="text-neutral-500 text-sm hidden sm:block">{{ currencySymbol() }}<span class="unit-price-text">{{ number_format($line['unit_price'], 0) }}</span></div>

                                {{-- Quantity Controls (Red minus, input, Green plus matching layout) --}}
                                <div class="flex items-center gap-1">
                                    <button type="button" class="w-8 h-8 flex items-center justify-center bg-[#d93c4a] hover:bg-[#c32f3c] text-white font-bold rounded-lg transition-colors shadow-sm select-none" onclick="changeCheckoutQty('{{ $line['key'] }}', -1)">−</button>
                                    <input type="number" readonly value="{{ $line['quantity'] }}" min="1" max="{{ $maxQty }}"
                                        class="w-12 h-8 text-center border border-neutral-200 rounded-lg text-sm font-semibold focus:outline-none qty-input bg-white" id="qty-input-{{ $line['key'] }}">
                                    <button type="button" class="w-8 h-8 flex items-center justify-center bg-[#28a745] hover:bg-[#218838] text-white font-bold rounded-lg transition-colors shadow-sm select-none" onclick="changeCheckoutQty('{{ $line['key'] }}', 1)">+</button>
                                </div>

                                {{-- Subtotal --}}
                                <div class="text-right font-bold text-ink sm:w-20 text-sm sm:text-base">{{ currencySymbol() }}<span class="item-subtotal-text font-bold" id="subtotal-{{ $line['key'] }}">{{ number_format($line['subtotal'], 0) }}</span></div>

                                {{-- Delete --}}
                                <button type="button" class="text-red-500 hover:text-red-700 transition-colors p-1" onclick="removeCheckoutItem('{{ $line['key'] }}')">
                                    <i class="ph ph-trash text-xl"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Shipping Details Card --}}
                <div class="bg-white border border-[color:var(--color-line)] rounded-2xl p-6">
                    <h3 class="font-semibold text-ink mb-4">{{ __('Shipping Details') }}</h3>

                {{-- Previous Ordered Address Block (Concept Image Mockup) --}}
                @if ($previousOrder)
                    <div class="mb-6 p-4 border border-emerald-600/30 bg-emerald-50/5 rounded-2xl" id="saved-address-section">
                        <label class="block text-xs font-bold text-neutral-500 uppercase tracking-wider mb-3">
                            {{ __('পূর্বের অর্ডারকৃত নাম ও ঠিকানা সিলেক্ট করুন অথবা নতুন ঠিকানা দিন *') }}
                        </label>
                        
                        <div id="saved-address-card" 
                            class="relative border-2 border-emerald-600 bg-emerald-50/20 rounded-xl p-4 cursor-pointer transition-all duration-200 shadow-sm">
                            <div class="space-y-2 text-sm text-neutral-800">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-neutral-900">{{ __('Name:') }}</span>
                                    <span id="saved-name-text" class="font-semibold text-neutral-800">{{ $previousOrder->customer_name }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="ph ph-phone-call text-emerald-600 text-lg"></i>
                                    <span class="font-bold text-neutral-900">{{ __('Phone:') }}</span>
                                    <span id="saved-phone-text" class="font-semibold text-neutral-800">{{ $previousOrder->customer_phone }}</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <i class="ph ph-house text-amber-600 text-lg mt-0.5"></i>
                                    <span class="font-bold text-neutral-900">{{ __('Address:') }}</span>
                                    <span id="saved-address-text" class="font-semibold text-neutral-800">
                                        {{ $previousOrder->shipping_address }}@if($previousOrder->city), {{ $previousOrder->city }}@endif @if($previousOrder->zip_code), {{ $previousOrder->zip_code }}@endif
                                    </span>
                                </div>
                            </div>
                            
                            <button type="button" id="btn-edit-saved" 
                                class="absolute right-3 bottom-3 inline-flex items-center gap-1 bg-neutral-800 hover:bg-neutral-700 text-white text-xs font-bold py-1.5 px-3 rounded-lg transition-all shadow-sm">
                                <i class="ph ph-pencil-simple text-xs"></i> {{ __('Edit') }}
                            </button>
                        </div>

                        <div class="mt-4">
                            <button type="button" id="btn-add-new-address" 
                                class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2.5 px-4 rounded-xl transition-all shadow-sm">
                                <i class="ph ph-plus text-xs"></i> {{ __('নতুন ঠিকানা যোগ করুন') }}
                            </button>
                        </div>
                    </div>
                @endif

                <div id="shipping-fields-container" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-1">
                        <label class="block text-sm font-medium mb-1">{{ __('Full Name') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="customer_name" value="{{ old('customer_name', auth()->check() ? auth()->user()->full_name : '') }}" required
                            class="w-full border border-[color:var(--color-line)] rounded-lg py-2.5 px-3.5 focus:outline-none focus:border-[color:var(--color-brand)]">
                        @error('customer_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-1">
                        <label class="block text-sm font-medium mb-1">{{ __('Phone') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="customer_phone" value="{{ old('customer_phone', auth()->check() ? auth()->user()->phone : '') }}" required
                            class="w-full border border-[color:var(--color-line)] rounded-lg py-2.5 px-3.5 focus:outline-none focus:border-[color:var(--color-brand)]">
                        @error('customer_phone')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium mb-1">{{ __('Email') }} <span class="text-neutral-400">({{ __('optional') }})</span></label>
                        <input type="email" name="customer_email" value="{{ old('customer_email', auth()->check() ? auth()->user()->email : '') }}"
                            class="w-full border border-[color:var(--color-line)] rounded-lg py-2.5 px-3.5 focus:outline-none focus:border-[color:var(--color-brand)]">
                        @error('customer_email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium mb-1">{{ __('Shipping Address') }} <span class="text-red-500">*</span></label>
                        <textarea name="shipping_address" rows="3" required
                            class="w-full border border-[color:var(--color-line)] rounded-lg py-2.5 px-3.5 focus:outline-none focus:border-[color:var(--color-brand)]">{{ old('shipping_address', auth()->check() ? auth()->user()->address : '') }}</textarea>
                        @error('shipping_address')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-semibold text-neutral-700 mb-2">{{ __('Delivery Area / ডেলিভারি এলাকা') }} <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="relative flex items-center justify-between p-4 border border-[color:var(--color-line)] rounded-xl cursor-pointer hover:bg-neutral-50/50 transition-all duration-200">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="shipping_area" value="inside" checked class="accent-[color:var(--color-brand)] h-4 w-4" onchange="calculateShipping()">
                                <div>
                                    <span class="font-bold text-neutral-800 text-sm block">{{ __('Inside Dhaka / ঢাকার ভিতরে') }}</span>
                                    <span class="text-xs text-neutral-500">{{ __('Cash on Delivery') }}</span>
                                </div>
                            </div>
                            <span class="font-bold text-neutral-900 text-sm" id="shipping-inside-preview">{{ currencySymbol() }}0</span>
                        </label>
                        
                        <label class="relative flex items-center justify-between p-4 border border-[color:var(--color-line)] rounded-xl cursor-pointer hover:bg-neutral-50/50 transition-all duration-200">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="shipping_area" value="outside" class="accent-[color:var(--color-brand)] h-4 w-4" onchange="calculateShipping()">
                                <div>
                                    <span class="font-bold text-neutral-800 text-sm block">{{ __('Outside Dhaka / ঢাকার বাইরে') }}</span>
                                    <span class="text-xs text-neutral-500">{{ __('Home Delivery') }}</span>
                                </div>
                            </div>
                            <span class="font-bold text-neutral-900 text-sm" id="shipping-outside-preview">{{ currencySymbol() }}0</span>
                        </label>
                    </div>
                    @error('shipping_area')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                @php($sslcommerzEnabled = \App\Services\Payment\SslCommerzService::isEnabled())

                <div class="mt-6">
                    <label class="block text-sm font-semibold text-neutral-700 mb-2">{{ __('Payment Method / পেমেন্ট পদ্ধতি') }} <span class="text-red-500">*</span></label>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 bg-[color:var(--color-brand-soft)] border border-[color:var(--color-line)] rounded-xl p-4 cursor-pointer">
                            <input type="radio" name="payment_method" value="cash_on_delivery" checked class="accent-[color:var(--color-brand)] h-4 w-4">
                            <div>
                                <div class="font-medium text-ink">{{ __('Cash on Delivery') }}</div>
                                <div class="text-sm text-[color:var(--color-muted)]">{{ __('Pay with cash when your order is delivered.') }}</div>
                            </div>
                            <i class="ph ph-money text-2xl text-[color:var(--color-brand)] ml-auto"></i>
                        </label>

                        @if ($sslcommerzEnabled)
                            <label class="flex items-center gap-3 bg-white border border-[color:var(--color-line)] rounded-xl p-4 cursor-pointer hover:bg-neutral-50/50 transition-all duration-200">
                                <input type="radio" name="payment_method" value="sslcommerz" class="accent-[color:var(--color-brand)] h-4 w-4">
                                <div>
                                    <div class="font-medium text-ink">{{ __('Pay Online (SSLCommerz)') }}</div>
                                    <div class="text-sm text-[color:var(--color-muted)]">{{ __('Card, Mobile Banking & Internet Banking. Secured by SSLCommerz.') }}</div>
                                </div>
                                <img src="{{ \App\Services\Payment\SslCommerzService::logo() }}" alt="SSLCommerz"
                                    class="h-8 w-auto ml-auto shrink-0">
                            </label>
                        @endif
                    </div>
                    @error('payment_method')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div> {{-- end of shipping details card --}}
        </div> {{-- end of left column container --}}

            {{-- Order summary --}}
            <div class="lg:col-span-1">
                <div class="bg-white border border-[color:var(--color-line)] rounded-2xl p-6 sticky top-24">
                    <h3 class="font-semibold text-ink mb-4">{{ __('Your Order') }}</h3>
                    <div class="space-y-3 max-h-64 overflow-y-auto" id="checkout-summary-items">
                        @foreach ($items as $line)
                            <div class="flex justify-between text-sm summary-item-row" id="summary-item-{{ $line['key'] }}">
                                <span class="text-[color:var(--color-muted)]">
                                    <span class="summary-item-name">{{ $line['product']->name }}</span>
                                    @if ($line['variant'])
                                        <span class="text-[color:var(--color-brand)]"> · {{ $line['variant']->name }}</span>
                                    @endif 
                                    <span class="text-xs font-semibold ml-1 text-ink">× <span class="summary-item-qty">{{ $line['quantity'] }}</span></span>
                                </span>
                                <span class="font-medium shrink-0 ml-2">{{ currencySymbol() }}<span class="summary-item-subtotal">{{ number_format($line['subtotal'], 0) }}</span></span>
                            </div>
                        @endforeach
                    </div>
                    <div class="border-t border-neutral-100 my-3"></div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-[color:var(--color-muted)]">{{ __('Subtotal') }}</span>
                        <span>{{ currencySymbol() }}<span id="checkout-subtotal-val">{{ number_format($subtotal, 0) }}</span></span>
                    </div>
                    
                    <div id="checkout-discount-container" class="flex justify-between text-sm mb-2 text-[color:var(--color-brand)] {{ $couponDiscount > 0 ? '' : 'hidden' }}">
                        <span>{{ __('Discount') }} <span class="text-xs">({{ $couponCode }})</span></span>
                        <span class="font-medium">−{{ currencySymbol() }}<span id="checkout-discount-val">{{ number_format($couponDiscount, 0) }}</span></span>
                    </div>

                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-[color:var(--color-muted)]">{{ __('Shipping') }}</span>
                        <span id="checkout-shipping-val">{{ $shippingCost > 0 ? amountWithSymbol($shippingCost) : __('Free') }}</span>
                    </div>
                    <div class="border-t border-neutral-100 my-3"></div>
                    <div class="flex justify-between font-bold text-ink text-lg">
                        <span>{{ __('Total') }}</span>
                        <span>{{ currencySymbol() }}<span id="checkout-total-val">{{ number_format(max(0, $subtotal - $couponDiscount) + $shippingCost, 0) }}</span></span>
                    </div>

                    <button type="submit"
                        class="mt-5 w-full bg-brand hover:bg-brand-dark text-white font-black py-3.5 rounded-xl transition-all duration-300 text-sm tracking-wider uppercase shadow-md hover:shadow-lg hover:-translate-y-0.5 transform flex items-center justify-center gap-2">
                        <i class="ph ph-shopping-bag text-base"></i>
                        <span>{{ __('Place Order') }}</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function changeCheckoutQty(lineKey, change) {
            const input = document.getElementById(`qty-input-${lineKey}`);
            if (!input) return;

            let newQty = parseInt(input.value) + change;
            const minQty = parseInt(input.min) || 1;
            const maxQty = parseInt(input.max) || 999;

            if (newQty < minQty) {
                removeCheckoutItem(lineKey);
                return;
            }
            if (newQty > maxQty) {
                alert("Only " + maxQty + " items are available in stock.");
                return;
            }

            input.value = newQty;
            updateCheckoutCartSession(lineKey, newQty);
        }

        function updateCheckoutCartSession(lineKey, qty) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch(`/cart/update/${lineKey}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ quantity: qty })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => { throw new Error(data.message || 'Failed to update quantity') });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    updateCheckoutDOM(lineKey, qty, data);
                }
            })
            .catch(error => {
                alert(error.message || 'Error updating cart');
                window.location.reload();
            });
        }

        function removeCheckoutItem(lineKey) {
            if (!confirm('Are you sure you want to remove this item?')) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(`/cart/remove/${lineKey}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => { throw new Error(data.message || 'Failed to remove item') });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Remove row from items list
                    const row = document.querySelector(`.checkout-item-row[data-line-key="${lineKey}"]`);
                    if (row) row.remove();

                    // Remove row from summary list
                    const summaryRow = document.getElementById(`summary-item-${lineKey}`);
                    if (summaryRow) summaryRow.remove();

                    // Check if cart is empty
                    const remainingRows = document.querySelectorAll('.checkout-item-row');
                    if (remainingRows.length === 0) {
                        window.location.reload();
                        return;
                    }

                    updateCheckoutDOM(lineKey, 0, data);
                }
            })
            .catch(error => {
                alert(error.message || 'Error removing item');
                window.location.reload();
            });
        }

        function calculateShipping() {
            let maxDhaka = 0;
            let maxOutside = 0;
            
            document.querySelectorAll('.checkout-item-row').forEach(row => {
                let dhaka = parseFloat(row.getAttribute('data-shipping-dhaka')) || 0;
                let outside = parseFloat(row.getAttribute('data-shipping-outside')) || 0;
                if (dhaka > maxDhaka) {
                    maxDhaka = dhaka;
                }
                if (outside > maxOutside) {
                    maxOutside = outside;
                }
            });
            
            const currency = "{{ currencySymbol() }}";
            const insidePreview = document.getElementById('shipping-inside-preview');
            const outsidePreview = document.getElementById('shipping-outside-preview');
            if (insidePreview) insidePreview.innerText = maxDhaka > 0 ? currency + maxDhaka.toFixed(0) : "{{ __('Free') }}";
            if (outsidePreview) outsidePreview.innerText = maxOutside > 0 ? currency + maxOutside.toFixed(0) : "{{ __('Free') }}";
            
            const selectedRadio = document.querySelector('input[name="shipping_area"]:checked');
            const selectedArea = selectedRadio ? selectedRadio.value : 'inside';
            const shippingCost = selectedArea === 'inside' ? maxDhaka : maxOutside;
            
            const shippingValSpan = document.getElementById('checkout-shipping-val');
            if (shippingValSpan) {
                shippingValSpan.innerText = shippingCost > 0 ? currency + shippingCost.toFixed(0) : "{{ __('Free') }}";
            }
            
            const subtotalValSpan = document.getElementById('checkout-subtotal-val');
            const discountValSpan = document.getElementById('checkout-discount-val');
            const totalValSpan = document.getElementById('checkout-total-val');
            
            if (subtotalValSpan && totalValSpan) {
                const subtotalText = subtotalValSpan.innerText.replace(/,/g, '');
                const subtotal = parseFloat(subtotalText) || 0;
                
                const discountText = discountValSpan ? discountValSpan.innerText.replace(/,/g, '') : '0';
                const discount = parseFloat(discountText) || 0;
                
                const total = Math.max(0, subtotal - discount) + shippingCost;
                totalValSpan.innerText = total.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
            }
        }

        // Run on page load
        document.addEventListener('DOMContentLoaded', function() {
            calculateShipping();
        });

        function updateCheckoutDOM(lineKey, qty, responseData) {
            const row = document.querySelector(`.checkout-item-row[data-line-key="${lineKey}"]`);
            if (row && qty > 0) {
                const unitPrice = parseFloat(row.getAttribute('data-unit-price'));
                const subtotal = unitPrice * qty;
                
                // Update item subtotal text in main list
                const subtotalSpan = document.getElementById(`subtotal-${lineKey}`);
                if (subtotalSpan) {
                    subtotalSpan.innerText = subtotal.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                }

                // Update item qty and subtotal in summary list
                const summaryRow = document.getElementById(`summary-item-${lineKey}`);
                if (summaryRow) {
                    const summaryQty = summaryRow.querySelector('.summary-item-qty');
                    const summarySubtotal = summaryRow.querySelector('.summary-item-subtotal');
                    if (summaryQty) summaryQty.innerText = qty;
                    if (summarySubtotal) {
                        summarySubtotal.innerText = subtotal.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                    }
                }
            }

            // Update global subtotal and total
            const subtotalValSpan = document.getElementById('checkout-subtotal-val');
            const discountContainer = document.getElementById('checkout-discount-container');
            const discountValSpan = document.getElementById('checkout-discount-val');

            if (responseData.subtotal !== undefined) {
                const subtotal = responseData.subtotal;
                if (subtotalValSpan) {
                    subtotalValSpan.innerText = subtotal.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                }

                const couponDiscount = responseData.couponDiscount || 0;
                if (discountValSpan) {
                    discountValSpan.innerText = couponDiscount.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                }
                if (discountContainer) {
                    if (couponDiscount > 0) {
                        discountContainer.classList.remove('hidden');
                    } else {
                        discountContainer.classList.add('hidden');
                    }
                }

                calculateShipping();
            }

            // Sync with cart badge and drawer
            document.querySelectorAll("[data-cart-count]").forEach(el => {
                el.textContent = responseData.count;
                el.classList.toggle("hidden", !responseData.count);
            });

            const drawerBody = document.querySelector("[data-cart-body]");
            if (drawerBody && responseData.drawer) {
                drawerBody.innerHTML = responseData.drawer;
            }
        }

        @if ($previousOrder)
            document.addEventListener('DOMContentLoaded', function() {
                const savedCard = document.getElementById('saved-address-card');
                const btnEditSaved = document.getElementById('btn-edit-saved');
                const btnAddNewAddress = document.getElementById('btn-add-new-address');
                const fieldsContainer = document.getElementById('shipping-fields-container');

                // Form inputs
                const inputName = document.querySelector('input[name="customer_name"]');
                const inputPhone = document.querySelector('input[name="customer_phone"]');
                const inputEmail = document.querySelector('input[name="customer_email"]');
                const inputAddress = document.querySelector('textarea[name="shipping_address"]');
                const inputCity = document.querySelector('input[name="city"]');
                const inputZip = document.querySelector('input[name="zip_code"]');

                // Saved data values from PHP
                const savedData = {
                    name: @json($previousOrder->customer_name),
                    phone: @json($previousOrder->customer_phone),
                    email: @json($previousOrder->customer_email),
                    address: @json($previousOrder->shipping_address),
                    city: @json($previousOrder->city),
                    zip: @json($previousOrder->zip_code)
                };

                function selectSavedAddress() {
                    savedCard.classList.remove('border-neutral-200', 'bg-white');
                    savedCard.classList.add('border-emerald-600', 'bg-emerald-50/20');
                    
                    // Populate values
                    inputName.value = savedData.name;
                    inputPhone.value = savedData.phone;
                    inputEmail.value = savedData.email || "{{ auth()->user()->email ?? '' }}";
                    inputAddress.value = savedData.address;
                    if (inputCity) inputCity.value = savedData.city || '';
                    if (inputZip) inputZip.value = savedData.zip || '';

                    fieldsContainer.classList.add('hidden');
                }

                function selectNewAddress() {
                    savedCard.classList.remove('border-emerald-600', 'bg-emerald-50/20');
                    savedCard.classList.add('border-neutral-200', 'bg-white');

                    // Clear shipping address fields
                    inputAddress.value = '';
                    if (inputCity) inputCity.value = '';
                    if (inputZip) inputZip.value = '';

                    fieldsContainer.classList.remove('hidden');
                }

                // If there are validation errors, default to showing the fields
                @if ($errors->any())
                    fieldsContainer.classList.remove('hidden');
                    savedCard.classList.remove('border-emerald-600', 'bg-emerald-50/20');
                    savedCard.classList.add('border-neutral-200', 'bg-white');
                @else
                    selectSavedAddress();
                @endif

                savedCard.addEventListener('click', function(e) {
                    if (e.target.closest('#btn-edit-saved')) return;
                    selectSavedAddress();
                });

                btnEditSaved.addEventListener('click', function(e) {
                    e.stopPropagation();
                    selectSavedAddress();
                    fieldsContainer.classList.remove('hidden');
                });

                btnAddNewAddress.addEventListener('click', function() {
                    selectNewAddress();
                });
            });
        @endif
    </script>
@endsection
