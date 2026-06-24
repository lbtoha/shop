@extends('shop.layouts.app')
@section('title', __('Order Successful') . ' — ' . config('application_info.company_info.name'))

@section('content')
<div class="min-h-[50vh] flex items-center justify-center bg-neutral-20/50 py-12 px-4">
    <div class="max-w-xl w-full bg-white p-8 sm:p-10 rounded-md border border-neutral-100/80 text-center">
        <div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-neutral-900 tracking-wide uppercase">
                {{ __('ORDER SUCCESSFUL!') }}
            </h2>
            <p class="mt-2 text-xs font-bold text-neutral-400 tracking-widest uppercase">
                {{ __('THANK YOU FOR YOUR PURCHASE') }}
            </p>
        </div>

        @php
            $statusMap = [
                'pending' => 1,
                'confirmed' => 2,
                'processing' => 3,
                'shipped' => 4,
                'delivered' => 5,
            ];
            $currentStep = $statusMap[$order->status->value] ?? 1;
            $isCancelled = $order->status->value === 'cancelled';
        @endphp

        {{-- Visual Tracking Timeline --}}
        <div class="bg-neutral-50/50 rounded-md p-6 my-6 border border-neutral-100/80 text-left">
            <h3 class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest mb-4 text-center">
                {{ __('Live Order Tracker') }}
            </h3>

            @if($isCancelled)
                <div class="p-4 rounded-md bg-rose-50 text-center text-xs text-rose-600 font-semibold border border-rose-100">
                    {{ __('This order has been cancelled.') }}
                </div>
            @else
                {{-- Desktop Timeline --}}
                <div class="hidden md:block py-3">
                    <div class="relative">
                        {{-- Line & Circles --}}
                        <div class="relative flex items-center justify-between h-8">
                            <div class="absolute left-[10%] right-[10%] top-1/2 -translate-y-1/2 h-0.5 bg-neutral-200/80 rounded-full -z-0"></div>
                            <div class="absolute left-[10%] top-1/2 -translate-y-1/2 h-0.5 bg-brand rounded-full transition-all duration-500 -z-0"
                                 style="width: {{ (($currentStep - 1) / 4) * 80 }}%"></div>

                            {{-- Step 1: Placed --}}
                            <div class="relative z-10 flex-1 flex justify-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300
                                    {{ $currentStep >= 1 ? 'bg-brand text-white' : 'bg-white border border-neutral-200 text-neutral-400' }}
                                    {{ $currentStep === 1 ? 'ring-4 ring-brand-soft animate-pulse' : '' }}">
                                    <i class="ph ph-receipt"></i>
                                </div>
                            </div>

                            {{-- Step 2: Confirmed --}}
                            <div class="relative z-10 flex-1 flex justify-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300
                                    {{ $currentStep >= 2 ? 'bg-brand text-white' : 'bg-white border border-neutral-200 text-neutral-400' }}
                                    {{ $currentStep === 2 ? 'ring-4 ring-brand-soft animate-pulse' : '' }}">
                                    <i class="ph ph-thumbs-up"></i>
                                </div>
                            </div>

                            {{-- Step 3: Processing --}}
                            <div class="relative z-10 flex-1 flex justify-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300
                                    {{ $currentStep >= 3 ? 'bg-brand text-white' : 'bg-white border border-neutral-200 text-neutral-400' }}
                                    {{ $currentStep === 3 ? 'ring-4 ring-brand-soft animate-pulse' : '' }}">
                                    <i class="ph ph-gear-six"></i>
                                </div>
                            </div>

                            {{-- Step 4: Shipped --}}
                            <div class="relative z-10 flex-1 flex justify-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300
                                    {{ $currentStep >= 4 ? 'bg-brand text-white' : 'bg-white border border-neutral-200 text-neutral-400' }}
                                    {{ $currentStep === 4 ? 'ring-4 ring-brand-soft animate-pulse' : '' }}">
                                    <i class="ph ph-truck"></i>
                                </div>
                            </div>

                            {{-- Step 5: Delivered --}}
                            <div class="relative z-10 flex-1 flex justify-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300
                                    {{ $currentStep >= 5 ? 'bg-brand text-white' : 'bg-white border border-neutral-200 text-neutral-400' }}
                                    {{ $currentStep === 5 ? 'ring-4 ring-brand-soft animate-pulse' : '' }}">
                                    <i class="ph ph-house-line"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Labels --}}
                        <div class="flex justify-between mt-2">
                            <span class="flex-1 text-center text-[9px] font-bold {{ $currentStep >= 1 ? 'text-ink' : 'text-neutral-400' }}">{{ __('Placed') }}</span>
                            <span class="flex-1 text-center text-[9px] font-bold {{ $currentStep >= 2 ? 'text-ink' : 'text-neutral-400' }}">{{ __('Confirmed') }}</span>
                            <span class="flex-1 text-center text-[9px] font-bold {{ $currentStep >= 3 ? 'text-ink' : 'text-neutral-400' }}">{{ __('Processing') }}</span>
                            <span class="flex-1 text-center text-[9px] font-bold {{ $currentStep >= 4 ? 'text-ink' : 'text-neutral-400' }}">{{ __('Shipped') }}</span>
                            <span class="flex-1 text-center text-[9px] font-bold {{ $currentStep >= 5 ? 'text-ink' : 'text-neutral-400' }}">{{ __('Delivered') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Mobile Timeline --}}
                <div class="block md:hidden space-y-4 relative pl-5 before:absolute before:left-1.5 before:top-1 before:bottom-1 before:w-0.5 before:bg-neutral-100">
                    <div class="relative flex items-start gap-3">
                        <div class="absolute -left-[18px] w-4.5 h-4.5 rounded-full flex items-center justify-center text-[9px] font-bold transition-all duration-300
                            {{ $currentStep >= 1 ? 'bg-brand text-white' : 'bg-white border border-neutral-200 text-neutral-400' }}
                            {{ $currentStep === 1 ? 'ring-2 ring-brand-soft animate-pulse' : '' }}">
                            1
                        </div>
                        <div>
                            <h4 class="text-xs font-bold {{ $currentStep >= 1 ? 'text-ink' : 'text-neutral-400' }}">{{ __('Order Placed') }}</h4>
                            <p class="text-[10px] text-muted">{{ __('We have received your order confirmation.') }}</p>
                        </div>
                    </div>
                    <div class="relative flex items-start gap-3">
                        <div class="absolute -left-[18px] w-4.5 h-4.5 rounded-full flex items-center justify-center text-[9px] font-bold transition-all duration-300
                            {{ $currentStep >= 2 ? 'bg-brand text-white' : 'bg-white border border-neutral-200 text-neutral-400' }}
                            {{ $currentStep === 2 ? 'ring-2 ring-brand-soft animate-pulse' : '' }}">
                            2
                        </div>
                        <div>
                            <h4 class="text-xs font-bold {{ $currentStep >= 2 ? 'text-ink' : 'text-neutral-400' }}">{{ __('Order Confirmed') }}</h4>
                            <p class="text-[10px] text-muted">{{ __('Your order details have been validated.') }}</p>
                        </div>
                    </div>
                    @if ($currentStep >= 3)
                        <div class="relative flex items-start gap-3">
                            <div class="absolute -left-[18px] w-4.5 h-4.5 rounded-full flex items-center justify-center text-[9px] font-bold transition-all duration-300
                                {{ $currentStep >= 3 ? 'bg-brand text-white' : 'bg-white border border-neutral-200 text-neutral-400' }}
                                {{ $currentStep === 3 ? 'ring-2 ring-brand-soft animate-pulse' : '' }}">
                                3
                            </div>
                            <div>
                                <h4 class="text-xs font-bold {{ $currentStep >= 3 ? 'text-ink' : 'text-neutral-400' }}">{{ __('Processing') }}</h4>
                                <p class="text-[10px] text-muted">{{ __('Preparing your parcel for dispatch.') }}</p>
                            </div>
                        </div>
                    @endif
                    @if ($currentStep >= 4)
                        <div class="relative flex items-start gap-3">
                            <div class="absolute -left-[18px] w-4.5 h-4.5 rounded-full flex items-center justify-center text-[9px] font-bold transition-all duration-300
                                {{ $currentStep >= 4 ? 'bg-brand text-white' : 'bg-white border border-neutral-200 text-neutral-400' }}
                                {{ $currentStep === 4 ? 'ring-2 ring-brand-soft animate-pulse' : '' }}">
                                4
                            </div>
                            <div>
                                <h4 class="text-xs font-bold {{ $currentStep >= 4 ? 'text-ink' : 'text-neutral-400' }}">{{ __('Shipped') }}</h4>
                                <p class="text-[10px] text-muted">{{ __('Your parcel is on the way with courier.') }}</p>
                            </div>
                        </div>
                    @endif
                    @if ($currentStep >= 5)
                        <div class="relative flex items-start gap-3">
                            <div class="absolute -left-[18px] w-4.5 h-4.5 rounded-full flex items-center justify-center text-[9px] font-bold transition-all duration-300
                                {{ $currentStep >= 5 ? 'bg-brand text-white' : 'bg-white border border-neutral-200 text-neutral-400' }}
                                {{ $currentStep === 5 ? 'ring-2 ring-brand-soft animate-pulse' : '' }}">
                                5
                            </div>
                            <div>
                                <h4 class="text-xs font-bold {{ $currentStep >= 5 ? 'text-ink' : 'text-neutral-400' }}">{{ __('Delivered') }}</h4>
                                <p class="text-[10px] text-muted">{{ __('Delivered successfully to your address.') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="bg-neutral-50 rounded-md p-6 my-8 space-y-4 border border-neutral-100/50 text-left">
            <div class="flex justify-between items-center text-sm">
                <span class="text-xs font-bold text-neutral-400 uppercase tracking-wider">{{ __('ORDER NUMBER') }}</span>
                <span class="font-extrabold text-neutral-900 tracking-wide">{{ $order->order_number }}</span>
            </div>
            <div class="border-t border-neutral-200/60 my-2"></div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="font-bold text-neutral-400 uppercase tracking-wider block mb-1">{{ __('Customer Details') }}</span>
                    <div class="font-bold text-neutral-800 text-sm">{{ $order->customer_name }}</div>
                    <div class="text-neutral-600 font-medium">{{ $order->customer_phone }}</div>
                    @if ($order->customer_email)<div class="text-neutral-500">{{ $order->customer_email }}</div>@endif
                </div>
                <div>
                    <span class="font-bold text-neutral-400 uppercase tracking-wider block mb-1">{{ __('Shipping Address') }}</span>
                    <div class="text-neutral-600 font-medium leading-relaxed">{{ $order->shipping_address }}</div>
                    @if ($order->city)<div class="text-neutral-600 font-medium">{{ $order->city }} {{ $order->zip_code }}</div>@endif
                </div>
            </div>

            <div class="border-t border-neutral-200/60 my-2"></div>
            
            <div class="space-y-2">
                <span class="font-bold text-neutral-400 uppercase tracking-wider block mb-1">{{ __('Order Items') }}</span>
                @foreach ($order->items as $item)
                    <div class="flex justify-between text-xs font-medium text-neutral-700">
                        <span>{{ $item->product_name }} @if ($item->variant_name)<span class="text-brand font-bold">({{ $item->variant_name }})</span>@endif <span class="text-neutral-400 font-bold">×{{ $item->quantity }}</span></span>
                        <span>{{ amountWithSymbol($item->subtotal) }}</span>
                    </div>
                @endforeach
            </div>

            <div class="border-t border-neutral-200/60 my-2"></div>

            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between text-neutral-500 font-medium">
                    <span>{{ __('Subtotal') }}</span>
                    <span>{{ amountWithSymbol($order->subtotal) }}</span>
                </div>
                @if ($order->discount > 0)
                    <div class="flex justify-between text-emerald-600 font-bold">
                        <span>{{ __('Discount') }} @if ($order->coupon_code)<span class="text-[10px]">({{ $order->coupon_code }})</span>@endif</span>
                        <span>−{{ amountWithSymbol($order->discount) }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-neutral-500 font-medium">
                    <span>{{ __('Shipping') }}</span>
                    <span>{{ $order->shipping_cost > 0 ? amountWithSymbol($order->shipping_cost) : __('Free') }}</span>
                </div>
                <div class="border-t border-neutral-200/40 pt-2 flex justify-between text-sm font-extrabold text-neutral-900">
                    <span>{{ __('TOTAL AMOUNT') }}</span>
                    <span class="text-lg text-brand">{{ amountWithSymbol($order->total) }}</span>
                </div>
            </div>
        </div>

        <div class="space-y-3">
            @if ($order->isOnlinePayable() && \App\Services\Payment\SslCommerzService::isEnabled())
                <a href="{{ route('shop.payment.sslcommerz.retry', $order->order_number) }}"
                    class="w-full flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 px-4 rounded-md transition duration-150 ease-in-out text-xs uppercase tracking-widest">
                    <i class="ph ph-credit-card text-base"></i>
                    <span>{{ __('PAY NOW') }} — {{ amountWithSymbol($order->total) }}</span>
                </a>
            @endif

            <a href="{{ route('shop.checkout.invoice', $order->order_number) }}" target="_blank"
                class="w-full flex items-center justify-center gap-2 bg-[#161c24] hover:bg-[#212b36] text-white font-bold py-3.5 px-4 rounded-md transition duration-150 ease-in-out text-xs uppercase tracking-widest">
                <i class="ph ph-file-text text-base"></i>
                <span>{{ __('DOWNLOAD INVOICE') }}</span>
            </a>

            <a href="{{ route('shop.track', ['order_number' => $order->order_number, 'phone' => $order->customer_phone]) }}"
                class="w-full flex items-center justify-center gap-2 bg-brand hover:bg-brand-dark text-white font-bold py-3.5 px-4 rounded-md transition duration-150 ease-in-out text-xs uppercase tracking-widest">
                <i class="ph ph-truck text-base"></i>
                <span>{{ __('TRACK ORDER LIVE') }}</span>
            </a>

            <a href="{{ route('shop.index') }}"
                class="w-full flex items-center justify-center gap-2 border border-neutral-200 hover:bg-neutral-50 text-neutral-700 font-bold py-3.5 px-4 rounded-md transition duration-150 ease-in-out text-xs uppercase tracking-widest">
                <i class="ph ph-shopping-bag text-base"></i>
                <span>{{ __('CONTINUE SHOPPING') }}</span>
            </a>
        </div>

        <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest mt-8">
            {{ __('WE WILL CALL YOU SHORTLY TO CONFIRM YOUR ORDER.') }}
        </p>
    </div>
</div>
@endsection
