<x-admin-app-layout>
    <div class="flex justify-between items-center gap-3 flex-wrap mb-6">
        <p class="l-text font-medium">{{ __('Order') }} #{{ $order->order_number }}</p>
        <div class="flex items-center gap-3">
            @foreach ($buttons as $button)
                <a href="{{ $button['link'] }}" class="btn-primary outlined !py-2">
                    <i class="{{ $button['icon'] }}"></i>
                    <span class="text-xs font-medium">{{ $button['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-12 gap-4 xxl:gap-6 overflow-x-hidden">
        {{-- Left column: order details --}}
        <div class="col-span-12 lg:col-span-8 space-y-4 xxl:space-y-6">
            {{-- Status summary --}}
            <div class="white-box">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="m-text font-medium mb-1">#{{ $order->order_number }}</p>
                        <span class="text-xs">{{ __('Placed') }}:
                            {{ $order->created_at->format('Y-m-d H:i A') }}
                            ({{ $order->created_at->diffForHumans() }})</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="status {{ $order->status->color() }} capitalize">{{ __($order->status->label()) }}</span>
                        <span class="status {{ $order->payment_status->color() }} capitalize">{{ __($order->payment_status->label()) }}</span>
                    </div>
                </div>
            </div>

            {{-- Line items --}}
            <div class="white-box">
                <p class="m-text font-medium mb-4">{{ __('Order Items') }}</p>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-neutral-30 dark:border-neutral-500">
                                <th class="py-2 pe-3 s-text font-medium">{{ __('Product') }}</th>
                                <th class="py-2 px-3 s-text font-medium text-end">{{ __('Price') }}</th>
                                <th class="py-2 px-3 s-text font-medium text-end">{{ __('Qty') }}</th>
                                <th class="py-2 ps-3 s-text font-medium text-end">{{ __('Subtotal') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr class="border-b border-neutral-20 dark:border-neutral-600">
                                    <td class="py-3 pe-3">
                                        <p class="s-text font-medium">{{ $item->product_name }}</p>
                                        @if ($item->product)
                                            <a href="{{ route('admin.products.edit', $item->product_id) }}"
                                                class="text-xs text-primary">{{ __('View product') }}</a>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 s-text text-end">{{ amountWithSymbol($item->price) }}</td>
                                    <td class="py-3 px-3 s-text text-end">{{ $item->quantity }}</td>
                                    <td class="py-3 ps-3 s-text text-end">{{ amountWithSymbol($item->subtotal) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex justify-end">
                    <div class="w-full max-w-xs space-y-2">
                        <div class="flex justify-between s-text">
                            <span>{{ __('Subtotal') }}</span>
                            <span class="font-medium">{{ amountWithSymbol($order->subtotal) }}</span>
                        </div>
                        <div class="flex justify-between s-text">
                            <span>{{ __('Shipping') }}</span>
                            <span class="font-medium">{{ amountWithSymbol($order->shipping_cost) }}</span>
                        </div>
                        <div
                            class="flex justify-between m-text font-medium pt-2 border-t border-neutral-30 dark:border-neutral-500">
                            <span>{{ __('Total') }}</span>
                            <span>{{ amountWithSymbol($order->total) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right column: customer / shipping / payment / update --}}
        <div class="col-span-12 lg:col-span-4 space-y-4 xxl:space-y-6">
            {{-- Customer --}}
            <div class="white-box">
                <p class="m-text font-medium mb-4">{{ __('Customer') }}</p>
                <div class="space-y-2 s-text">
                    <p><span class="text-xs block">{{ __('Name') }}</span>{{ $order->customer_name }}</p>
                    <p><span class="text-xs block">{{ __('Phone') }}</span>{{ $order->customer_phone }}</p>
                    @if ($order->customer_email)
                        <p><span class="text-xs block">{{ __('Email') }}</span>{{ $order->customer_email }}</p>
                    @endif
                </div>
            </div>

            {{-- Shipping --}}
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

            {{-- Payment --}}
            <div class="white-box">
                <p class="m-text font-medium mb-4">{{ __('Payment') }}</p>
                <div class="space-y-2 s-text">
                    <p><span class="text-xs block">{{ __('Method') }}</span>{{ __('Cash on Delivery') }}</p>
                    <p>
                        <span class="text-xs block">{{ __('Payment Status') }}</span>
                        <span
                            class="status {{ $order->payment_status->color() }} capitalize">{{ __($order->payment_status->label()) }}</span>
                    </p>
                </div>
            </div>

            {{-- Update form --}}
            <div class="white-box">
                <p class="m-text font-medium mb-4">{{ __('Update Order') }}</p>
                <form action="{{ route('admin.orders.update-status', $order->id) }}" class="form-submit-edit"
                    method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <x-admin::label for="status">{{ __('Order Status') }}</x-admin::label>
                            <x-admin::select-option name="status" id="status" placeholder="{{ __('Select Status') }}">
                                @foreach (\App\Enums\OrderStatusEnum::cases() as $case)
                                    <option value="{{ $case->value }}" @selected($order->status->value === $case->value)>
                                        {{ __($case->label()) }}
                                    </option>
                                @endforeach
                            </x-admin::select-option>
                        </div>
                        <div>
                            <x-admin::label for="payment_status">{{ __('Payment Status') }}</x-admin::label>
                            <x-admin::select-option name="payment_status" id="payment_status"
                                placeholder="{{ __('Select Payment Status') }}">
                                @foreach (\App\Enums\OrderPaymentStatusEnum::cases() as $case)
                                    <option value="{{ $case->value }}" @selected($order->payment_status->value === $case->value)>
                                        {{ __($case->label()) }}
                                    </option>
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
