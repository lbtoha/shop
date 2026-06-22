<x-admin-app-layout>
    @php
        $pipeline = \App\Enums\OrderStatusEnum::pipeline();
        $isCancelled = $order->status === \App\Enums\OrderStatusEnum::CANCELLED;
        $currentIndex = array_search($order->status, $pipeline, true);
        $next = $order->status->next();
    @endphp

    <div class="flex justify-between items-center gap-3 flex-wrap mb-6">
        <div>
            <div class="flex items-center gap-3">
                <p class="l-text font-medium">{{ __('Order') }} #{{ $order->order_number }}</p>
                <span class="status {{ $order->status->color() }} capitalize">{{ __($order->status->label()) }}</span>
                <span class="status {{ $order->payment_status->color() }} capitalize">{{ __($order->payment_status->label()) }}</span>
                @if (($order->source ?? 'storefront') === 'manual')
                    <span class="status info capitalize"><i class="ph ph-storefront"></i> {{ __('Manual') }}</span>
                @endif
            </div>
            <span class="text-xs text-neutral-400 block mt-1">{{ $order->created_at->format('M d, Y · g:i A') }} ({{ $order->created_at->diffForHumans() }})</span>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ \App\Services\Notification\OrderMessages::whatsappLink($order) }}" target="_blank" rel="noopener"
                class="btn-primary outlined !py-2 !border-success !text-success">
                <i class="ph ph-whatsapp-logo"></i><span class="text-xs font-medium">{{ __('Notify on WhatsApp') }}</span>
            </a>
            <a href="{{ route('admin.orders.invoice', $order->id) }}" class="btn-primary outlined !py-2">
                <i class="ph ph-file-pdf"></i><span class="text-xs font-medium">{{ __('Invoice') }}</span>
            </a>

            {{-- Quick Payment Status Update Button --}}
            @if ($order->payment_status->value === 'unpaid')
                <button type="button" class="action-confirm-btn flex items-center gap-1 rounded bg-emerald-100 text-emerald-700 hover:bg-emerald-200 px-3 py-1.5 text-xs font-semibold cursor-pointer"
                    action="{{ route('admin.orders.update-status', $order->id) }}"
                    method="PUT"
                    title="{{ __('Mark as Paid?') }}"
                    text="{{ __('Do you want to mark this order as Paid?') }}"
                    data-payment_status="paid">
                    <i class="ph ph-credit-card text-sm"></i>
                    <span>{{ __('Mark as Paid') }}</span>
                </button>
            @endif

            {{-- Quick Order Status Update Buttons --}}
            @if ($order->status->value === 'pending')
                <button type="button" class="action-confirm-btn flex items-center gap-1 rounded bg-green-100 text-green-700 hover:bg-green-200 px-3 py-1.5 text-xs font-semibold cursor-pointer"
                    action="{{ route('admin.orders.update-status', $order->id) }}"
                    method="PUT"
                    title="{{ __('Confirm Order?') }}"
                    text="{{ __('Do you want to confirm this order?') }}"
                    data-status="confirmed">
                    <i class="ph ph-check-circle text-sm"></i>
                    <span>{{ __('Confirm Order') }}</span>
                </button>
                <button type="button" class="action-confirm-btn flex items-center gap-1 rounded bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1.5 text-xs font-semibold cursor-pointer"
                    action="{{ route('admin.orders.update-status', $order->id) }}"
                    method="PUT"
                    title="{{ __('Cancel Order?') }}"
                    text="{{ __('Do you want to cancel this order?') }}"
                    data-status="cancelled">
                    <i class="ph ph-x-circle text-sm"></i>
                    <span>{{ __('Cancel Order') }}</span>
                </button>
            @elseif ($order->status->value === 'confirmed')
                <button type="button" class="action-confirm-btn flex items-center gap-1 rounded bg-blue-100 text-blue-700 hover:bg-blue-200 px-3 py-1.5 text-xs font-semibold cursor-pointer"
                    action="{{ route('admin.orders.update-status', $order->id) }}"
                    method="PUT"
                    title="{{ __('Process Order?') }}"
                    text="{{ __('Do you want to start processing this order?') }}"
                    data-status="processing">
                    <i class="ph ph-gear-six text-sm"></i>
                    <span>{{ __('Process Order') }}</span>
                </button>
            @elseif ($order->status->value === 'processing')
                <button type="button" class="action-confirm-btn flex items-center gap-1 rounded bg-indigo-100 text-indigo-700 hover:bg-indigo-200 px-3 py-1.5 text-xs font-semibold cursor-pointer"
                    action="{{ route('admin.orders.update-status', $order->id) }}"
                    method="PUT"
                    title="{{ __('Ship Order?') }}"
                    text="{{ __('Do you want to mark this order as shipped?') }}"
                    data-status="shipped">
                    <i class="ph ph-truck text-sm"></i>
                    <span>{{ __('Ship Order') }}</span>
                </button>
            @elseif ($order->status->value === 'shipped')
                <button type="button" class="action-confirm-btn flex items-center gap-1 rounded bg-green-100 text-green-700 hover:bg-green-200 px-3 py-1.5 text-xs font-semibold cursor-pointer"
                    action="{{ route('admin.orders.update-status', $order->id) }}"
                    method="PUT"
                    title="{{ __('Deliver Order?') }}"
                    text="{{ __('Do you want to mark this order as delivered?') }}"
                    data-status="delivered">
                    <i class="ph ph-package text-sm"></i>
                    <span>{{ __('Deliver Order') }}</span>
                </button>
            @endif

            @foreach ($buttons as $button)
                <a href="{{ $button['link'] }}" class="btn-primary outlined !py-2">
                    <i class="{{ $button['icon'] }}"></i><span class="text-xs font-medium">{{ $button['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Status timeline --}}
    <div class="white-box mb-4 xxl:mb-6">
        @if ($isCancelled)
            <div class="flex items-center gap-2 text-danger">
                <i class="ph ph-x-circle text-2xl"></i>
                <span class="m-text font-medium">{{ __('This order was cancelled.') }}</span>
            </div>
        @else
            <div class="flex items-center justify-between">
                @foreach ($pipeline as $i => $step)
                    @php($done = $i <= $currentIndex)
                    <div class="flex-1 flex flex-col items-center text-center relative">
                        @if (! $loop->first)
                            <span class="absolute top-4 right-1/2 w-full h-0.5 {{ $i <= $currentIndex ? 'bg-primary' : 'bg-neutral-30 dark:bg-neutral-600' }}"></span>
                        @endif
                        <span class="relative z-10 w-8 h-8 rounded-full flex items-center justify-center {{ $done ? 'bg-primary text-white' : 'bg-neutral-20 dark:bg-neutral-600 text-neutral-400' }}">
                            @if ($done)<i class="ph ph-check"></i>@else<span class="text-xs">{{ $i + 1 }}</span>@endif
                        </span>
                        <span class="text-xs mt-2 {{ $done ? 'font-medium' : 'text-neutral-400' }}">{{ __($step->label()) }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="grid grid-cols-12 gap-4 xxl:gap-6 overflow-x-hidden">
        {{-- Left: items --}}
        <div class="col-span-12 lg:col-span-8 space-y-4 xxl:space-y-6">
            <div class="white-box">
                <p class="m-text font-medium mb-4">{{ __('Order Items') }} ({{ $order->items->count() }})</p>
                <div class="space-y-3">
                    @foreach ($order->items as $item)
                        <div class="flex items-center gap-3 p-3 rounded-lg border border-neutral-20 dark:border-neutral-600">
                            <div class="w-14 h-14 rounded-md overflow-hidden bg-neutral-10 dark:bg-neutral-700 shrink-0 flex items-center justify-center">
                                @if ($item->product?->thumbnail)
                                    <img src="{{ $item->product->thumbnail }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <i class="ph ph-image text-xl text-neutral-400"></i>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="s-text font-medium truncate">{{ $item->product_name }}</p>
                                @if ($item->variant_name)
                                    <span class="text-xs text-primary block font-medium">{{ $item->variant_name }}</span>
                                @endif
                                <span class="text-xs text-neutral-500 block">{{ amountWithSymbol($item->price) }} × {{ $item->quantity }}</span>
                                @if ($item->product)
                                    <div class="flex items-center gap-2 mt-1 flex-wrap text-xs">
                                        <a href="{{ route('admin.products.edit', $item->product_id) }}"
                                            class="text-primary hover:underline font-medium">{{ __('View Product') }}</a>
                                        <span class="text-neutral-300 dark:text-neutral-600">|</span>
                                        <span>
                                            {{ __('Stock') }}: <strong class="{{ $item->product->stock <= 0 ? 'text-danger' : 'text-neutral-500' }}">{{ $item->product->stock }}</strong>
                                        </span>
                                        <span class="text-neutral-300 dark:text-neutral-600">|</span>
                                        <button type="button" class="action-confirm-btn font-semibold {{ $item->product->is_active ? 'text-success hover:underline' : 'text-danger hover:underline' }}"
                                            action="{{ route('admin.products.toggle-status', $item->product->id) }}"
                                            method="POST"
                                            title="{{ __('Toggle Product Status?') }}"
                                            text="{{ __('Change status of :name to :status?', ['name' => $item->product->name, 'status' => $item->product->is_active ? __('Inactive') : __('Active')]) }}">
                                            {{ $item->product->is_active ? __('Active') : __('Inactive') }}
                                        </button>
                                    </div>
                                @else
                                    <span class="text-xs text-neutral-400">({{ __('Product deleted') }})</span>
                                @endif
                            </div>
                            <div class="s-text font-medium text-end shrink-0">{{ amountWithSymbol($item->subtotal) }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 flex justify-end">
                    <div class="w-full max-w-xs space-y-2">
                        <div class="flex justify-between s-text"><span>{{ __('Subtotal') }}</span><span class="font-medium">{{ amountWithSymbol($order->subtotal) }}</span></div>
                        @if ($order->discount > 0)
                            <div class="flex justify-between s-text text-success"><span>{{ __('Discount') }} @if ($order->coupon_code)<span class="text-xs">({{ $order->coupon_code }})</span>@endif</span><span class="font-medium">−{{ amountWithSymbol($order->discount) }}</span></div>
                        @endif
                        <div class="flex justify-between s-text"><span>{{ __('Shipping') }}</span><span class="font-medium">{{ amountWithSymbol($order->shipping_cost) }}</span></div>
                        <div class="flex justify-between m-text font-medium pt-2 border-t border-neutral-30 dark:border-neutral-500"><span>{{ __('Total') }}</span><span>{{ amountWithSymbol($order->total) }}</span></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: customer / shipping / update --}}
        <div class="col-span-12 lg:col-span-4 space-y-4 xxl:space-y-6">
            <div class="white-box">
                <p class="m-text font-medium mb-4">{{ __('Customer') }}</p>
                <div class="space-y-2 s-text">
                    <p><span class="text-xs block">{{ __('Name') }}</span>{{ $order->customer_name }}</p>
                    <p><span class="text-xs block">{{ __('Phone') }}</span>
                        <a href="tel:{{ $order->customer_phone }}" class="text-primary">{{ $order->customer_phone }}</a>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_phone) }}" target="_blank" class="text-success ms-2"><i class="ph ph-whatsapp-logo"></i></a>
                    </p>
                    @if ($order->customer_email)
                        <p><span class="text-xs block">{{ __('Email') }}</span>{{ $order->customer_email }}</p>
                    @endif
                </div>
            </div>

            <div class="white-box">
                <p class="m-text font-medium mb-4">{{ __('Shipping Address') }}</p>
                <div class="space-y-2 s-text">
                    <p>{{ $order->shipping_address }}</p>
                    <p>{{ $order->city }} @if ($order->zip_code) - {{ $order->zip_code }} @endif</p>
                    @if ($order->note)
                        <p><span class="text-xs block">{{ __('Note') }}</span>{{ $order->note }}</p>
                    @endif
                </div>
            </div>

            <div class="white-box">
                <p class="m-text font-medium mb-4">{{ __('Payment') }}</p>
                <div class="space-y-2 s-text">
                    <p><span class="text-xs block">{{ __('Method') }}</span>
                        @if ($order->payment_method === 'sslcommerz')
                            <span class="inline-flex items-center gap-1"><i class="ph ph-credit-card"></i> {{ __('SSLCommerz (Online)') }}</span>
                        @else
                            <span class="inline-flex items-center gap-1"><i class="ph ph-money"></i> {{ __('Cash on Delivery') }}</span>
                        @endif
                    </p>
                    @if ($order->transaction_id)
                        <p><span class="text-xs block">{{ __('Transaction ID') }}</span>{{ $order->transaction_id }}</p>
                    @endif
                    @if ($order->gateway_transaction_id)
                        <p><span class="text-xs block">{{ __('Bank Transaction ID') }}</span>{{ $order->gateway_transaction_id }}</p>
                    @endif
                </div>
            </div>

            <div class="white-box">
                <p class="m-text font-medium mb-4">{{ __('Update Order') }}</p>
                <form action="{{ route('admin.orders.update-status', $order->id) }}" class="form-submit-edit" method="POST">
                    @csrf @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <x-admin::label for="status">{{ __('Order Status') }}</x-admin::label>
                            <x-admin::select-option name="status" id="status" placeholder="{{ __('Select Status') }}">
                                @foreach (\App\Enums\OrderStatusEnum::cases() as $case)
                                    <option value="{{ $case->value }}" @selected($order->status->value === $case->value)>{{ __($case->label()) }}</option>
                                @endforeach
                            </x-admin::select-option>
                        </div>
                        <div>
                            <x-admin::label for="payment_status">{{ __('Payment Status') }}</x-admin::label>
                            <x-admin::select-option name="payment_status" id="payment_status" placeholder="{{ __('Select Payment Status') }}">
                                @foreach (\App\Enums\OrderPaymentStatusEnum::cases() as $case)
                                    <option value="{{ $case->value }}" @selected($order->payment_status->value === $case->value)>{{ __($case->label()) }}</option>
                                @endforeach
                            </x-admin::select-option>
                        </div>
                    </div>
                    <div class="flex items-center justify-end mt-4">
                        <x-admin::primary-button type="submit">{{ __('Save') }}</x-admin::primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-app-layout>
