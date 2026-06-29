@extends('shop.layouts.app')
@section('title', __('Order Successful') . ' — ' . config('application_info.company_info.name'))

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8 sm:py-12">
    {{-- Success Checkmark & Message --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-50 text-emerald-500 mb-4 relative">
            <span class="absolute inset-0 rounded-full bg-emerald-500/10 animate-ping duration-1000"></span>
            <i class="ph-fill ph-check-circle text-4xl"></i>
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold text-neutral-900 tracking-tight">
            {{ __('Order Placed Successfully!') }}
        </h1>
        <p class="mt-2.5 text-sm sm:text-base text-neutral-600 leading-relaxed max-w-md mx-auto">
            {{ __('Thank you for your purchase. We have received your order and are processing it.') }}
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

    {{-- Main Container Card --}}
    <div class="space-y-6">
        {{-- Tracker Timeline Card --}}
        <div class="bg-white rounded-xl p-5 border border-neutral-100 shadow-[0_2px_12px_rgba(0,0,0,0.015)]">
            <div class="flex items-center justify-between border-b border-neutral-100 pb-3.5 mb-5">
                <h3 class="text-sm font-semibold text-neutral-500 uppercase tracking-wider flex items-center gap-1.5">
                    <i class="ph ph-activity text-brand"></i>
                    <span>{{ __('Live Order Tracker') }}</span>
                </h3>
                <span class="text-xs font-semibold text-brand bg-brand/5 px-2.5 py-1 rounded-full uppercase tracking-wider">
                    {{ $order->status->value }}
                </span>
            </div>

            @if($isCancelled)
                <div class="p-4 rounded-lg bg-rose-50 text-center text-sm sm:text-base text-rose-600 font-medium border border-rose-100">
                    {{ __('This order has been cancelled.') }}
                </div>
            @else
                {{-- Desktop Timeline --}}
                <div class="hidden md:block py-2">
                    <div class="relative">
                        {{-- Line & Circles --}}
                        <div class="relative flex items-center justify-between h-10">
                            <div class="absolute left-[10%] right-[10%] top-1/2 -translate-y-1/2 h-1 bg-neutral-100 rounded-full -z-0"></div>
                            <div class="absolute left-[10%] top-1/2 -translate-y-1/2 h-1 bg-brand rounded-full transition-all duration-500 -z-0"
                                 style="width: {{ (($currentStep - 1) / 4) * 80 }}%"></div>

                            {{-- Step 1: Placed --}}
                            <div class="relative z-10 flex-1 flex justify-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-medium transition-all duration-300
                                    {{ $currentStep >= 1 ? 'bg-brand text-white shadow-md shadow-brand/25' : 'bg-white border-2 border-neutral-200 text-neutral-400' }}
                                    {{ $currentStep === 1 ? 'ring-4 ring-brand/10 animate-pulse' : '' }}">
                                    <i class="ph ph-receipt text-base"></i>
                                </div>
                            </div>

                            {{-- Step 2: Confirmed --}}
                            <div class="relative z-10 flex-1 flex justify-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-medium transition-all duration-300
                                    {{ $currentStep >= 2 ? 'bg-brand text-white shadow-md shadow-brand/25' : 'bg-white border-2 border-neutral-200 text-neutral-400' }}
                                    {{ $currentStep === 2 ? 'ring-4 ring-brand/10 animate-pulse' : '' }}">
                                    <i class="ph ph-thumbs-up text-base"></i>
                                </div>
                            </div>

                            {{-- Step 3: Processing --}}
                            <div class="relative z-10 flex-1 flex justify-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-medium transition-all duration-300
                                    {{ $currentStep >= 3 ? 'bg-brand text-white shadow-md shadow-brand/25' : 'bg-white border-2 border-neutral-200 text-neutral-400' }}
                                    {{ $currentStep === 3 ? 'ring-4 ring-brand/10 animate-pulse' : '' }}">
                                    <i class="ph ph-gear-six text-base"></i>
                                </div>
                            </div>

                            {{-- Step 4: Shipped --}}
                            <div class="relative z-10 flex-1 flex justify-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-medium transition-all duration-300
                                    {{ $currentStep >= 4 ? 'bg-brand text-white shadow-md shadow-brand/25' : 'bg-white border-2 border-neutral-200 text-neutral-400' }}
                                    {{ $currentStep === 4 ? 'ring-4 ring-brand/10 animate-pulse' : '' }}">
                                    <i class="ph ph-truck text-base"></i>
                                </div>
                            </div>

                            {{-- Step 5: Delivered --}}
                            <div class="relative z-10 flex-1 flex justify-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-medium transition-all duration-300
                                    {{ $currentStep >= 5 ? 'bg-brand text-white shadow-md shadow-brand/25' : 'bg-white border-2 border-neutral-200 text-neutral-400' }}
                                    {{ $currentStep === 5 ? 'ring-4 ring-brand/10 animate-pulse' : '' }}">
                                    <i class="ph ph-house-line text-base"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Labels --}}
                        <div class="flex justify-between mt-3">
                            <span class="flex-1 text-center text-xs sm:text-sm font-medium {{ $currentStep >= 1 ? 'text-neutral-900 font-semibold' : 'text-neutral-500' }}">{{ __('Placed') }}</span>
                            <span class="flex-1 text-center text-xs sm:text-sm font-medium {{ $currentStep >= 2 ? 'text-neutral-900 font-semibold' : 'text-neutral-500' }}">{{ __('Confirmed') }}</span>
                            <span class="flex-1 text-center text-xs sm:text-sm font-medium {{ $currentStep >= 3 ? 'text-neutral-900 font-semibold' : 'text-neutral-500' }}">{{ __('Processing') }}</span>
                            <span class="flex-1 text-center text-xs sm:text-sm font-medium {{ $currentStep >= 4 ? 'text-neutral-900 font-semibold' : 'text-neutral-500' }}">{{ __('Shipped') }}</span>
                            <span class="flex-1 text-center text-xs sm:text-sm font-medium {{ $currentStep >= 5 ? 'text-neutral-900 font-semibold' : 'text-neutral-500' }}">{{ __('Delivered') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Mobile Timeline --}}
                <div class="block md:hidden space-y-6">
                    {{-- Step 1 --}}
                    <div class="relative pl-8">
                        @if ($currentStep > 1)
                            <div class="absolute left-[11px] top-6 bottom-[-24px] w-0.5 bg-brand"></div>
                        @else
                            <div class="absolute left-[11px] top-6 bottom-[-24px] w-0.5 bg-neutral-100"></div>
                        @endif
                        <div class="absolute left-0 top-0.5 w-6 h-6 rounded-full flex items-center justify-center text-xs font-medium transition-all duration-300
                            {{ $currentStep >= 1 ? 'bg-brand text-white shadow-sm' : 'bg-white border border-neutral-200 text-neutral-400' }}
                            {{ $currentStep === 1 ? 'ring-4 ring-brand/10' : '' }}">
                            1
                        </div>
                        <div>
                            <h4 class="text-sm sm:text-base font-semibold {{ $currentStep >= 1 ? 'text-neutral-900' : 'text-neutral-400' }}">{{ __('Order Placed') }}</h4>
                            <p class="text-xs sm:text-sm text-neutral-500 mt-0.5">{{ __('We have received your order confirmation.') }}</p>
                        </div>
                    </div>

                    {{-- Step 2 --}}
                    <div class="relative pl-8">
                        @if ($currentStep >= 3)
                            <div class="absolute left-[11px] top-6 bottom-[-24px] w-0.5 bg-brand"></div>
                        @elseif ($currentStep >= 2)
                            <div class="absolute left-[11px] top-6 bottom-[-24px] w-0.5 bg-neutral-100"></div>
                        @endif
                        <div class="absolute left-0 top-0.5 w-6 h-6 rounded-full flex items-center justify-center text-xs font-medium transition-all duration-300
                            {{ $currentStep >= 2 ? 'bg-brand text-white shadow-sm' : 'bg-white border border-neutral-200 text-neutral-400' }}
                            {{ $currentStep === 2 ? 'ring-4 ring-brand/10 animate-pulse' : '' }}">
                            2
                        </div>
                        <div>
                            <h4 class="text-sm sm:text-base font-semibold {{ $currentStep >= 2 ? 'text-neutral-900' : 'text-neutral-400' }}">{{ __('Order Confirmed') }}</h4>
                            <p class="text-xs sm:text-sm text-neutral-500 mt-0.5">{{ __('Your order details have been validated.') }}</p>
                        </div>
                    </div>

                    {{-- Step 3 --}}
                    @if ($currentStep >= 3)
                        <div class="relative pl-8">
                            @if ($currentStep >= 4)
                                <div class="absolute left-[11px] top-6 bottom-[-24px] w-0.5 bg-brand"></div>
                            @elseif ($currentStep >= 3)
                                <div class="absolute left-[11px] top-6 bottom-[-24px] w-0.5 bg-neutral-100"></div>
                            @endif
                            <div class="absolute left-0 top-0.5 w-6 h-6 rounded-full flex items-center justify-center text-xs font-medium transition-all duration-300
                                {{ $currentStep >= 3 ? 'bg-brand text-white shadow-sm' : 'bg-white border border-neutral-200 text-neutral-400' }}
                                {{ $currentStep === 3 ? 'ring-4 ring-brand/10 animate-pulse' : '' }}">
                                3
                            </div>
                            <div>
                                <h4 class="text-sm sm:text-base font-semibold {{ $currentStep >= 3 ? 'text-neutral-900' : 'text-neutral-400' }}">{{ __('Processing') }}</h4>
                                <p class="text-xs sm:text-sm text-neutral-500 mt-0.5">{{ __('Preparing your parcel for dispatch.') }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Step 4 --}}
                    @if ($currentStep >= 4)
                        <div class="relative pl-8">
                            @if ($currentStep >= 5)
                                <div class="absolute left-[11px] top-6 bottom-[-24px] w-0.5 bg-brand"></div>
                            @elseif ($currentStep >= 4)
                                <div class="absolute left-[11px] top-6 bottom-[-24px] w-0.5 bg-neutral-100"></div>
                            @endif
                            <div class="absolute left-0 top-0.5 w-6 h-6 rounded-full flex items-center justify-center text-xs font-medium transition-all duration-300
                                {{ $currentStep >= 4 ? 'bg-brand text-white shadow-sm' : 'bg-white border border-neutral-200 text-neutral-400' }}
                                {{ $currentStep === 4 ? 'ring-4 ring-brand/10 animate-pulse' : '' }}">
                                4
                            </div>
                            <div>
                                <h4 class="text-sm sm:text-base font-semibold {{ $currentStep >= 4 ? 'text-neutral-900' : 'text-neutral-400' }}">{{ __('Shipped') }}</h4>
                                <p class="text-xs sm:text-sm text-neutral-500 mt-0.5">{{ __('Your parcel is on the way with courier.') }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Step 5 --}}
                    @if ($currentStep >= 5)
                        <div class="relative pl-8">
                            <div class="absolute left-0 top-0.5 w-6 h-6 rounded-full flex items-center justify-center text-xs font-medium transition-all duration-300
                                {{ $currentStep >= 5 ? 'bg-brand text-white shadow-sm' : 'bg-white border border-neutral-200 text-neutral-400' }}
                                {{ $currentStep === 5 ? 'ring-4 ring-brand/10 animate-pulse' : '' }}">
                                5
                            </div>
                            <div>
                                <h4 class="text-sm sm:text-base font-semibold {{ $currentStep >= 5 ? 'text-neutral-900' : 'text-neutral-400' }}">{{ __('Delivered') }}</h4>
                                <p class="text-xs sm:text-sm text-neutral-500 mt-0.5">{{ __('Delivered successfully to your address.') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- Order Items Summary Card --}}
        <div class="bg-white rounded-xl p-5 border border-neutral-100 shadow-[0_2px_12px_rgba(0,0,0,0.015)]">
            <div class="flex justify-between items-center border-b border-neutral-100 pb-3 mb-4">
                <h3 class="text-sm sm:text-base font-semibold text-neutral-900 flex items-center gap-2">
                    <i class="ph ph-shopping-bag-open text-brand"></i>
                    <span>{{ __('Order Summary') }}</span>
                </h3>
                <span class="text-xs sm:text-sm font-semibold text-brand bg-brand/5 px-2.5 py-1 rounded-full">
                    #{{ $order->order_number }}
                </span>
            </div>

            {{-- Products List --}}
            <div class="divide-y divide-neutral-100">
                @foreach ($order->items as $item)
                    <div class="flex items-center gap-4 py-3.5">
                        {{-- Image Thumbnail --}}
                        <div class="w-12 h-16 shrink-0 rounded-md bg-neutral-50 border border-neutral-100 overflow-hidden flex items-center justify-center">
                            @if($item->product && $item->product->thumbnail)
                                <img src="{{ $item->product->thumbnail }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                            @else
                                <i class="ph ph-package text-xl text-neutral-300"></i>
                            @endif
                        </div>

                        {{-- Title and Qty --}}
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm sm:text-base font-medium text-neutral-900 leading-tight line-clamp-2">
                                {{ $item->product_name }}
                            </h4>
                            <div class="text-xs sm:text-sm text-neutral-600 mt-1 flex flex-wrap items-center gap-x-2 gap-y-0.5">
                                @if ($item->variant_name)
                                    <span class="text-[color:var(--color-brand)] font-medium">{{ $item->variant_name }}</span>
                                    <span class="text-neutral-300">•</span>
                                @endif
                                <span>Qty: {{ $item->quantity }}</span>
                            </div>
                        </div>

                        {{-- Subtotal --}}
                        <div class="text-sm sm:text-base font-semibold text-neutral-900 shrink-0 ml-3">
                            {{ amountWithSymbol($item->subtotal) }}
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Financials Breakdown --}}
            <div class="border-t border-neutral-100 pt-4 mt-2 space-y-2.5 text-sm sm:text-base">
                <div class="flex justify-between text-neutral-600 font-normal">
                    <span>{{ __('Subtotal') }}</span>
                    <span class="text-neutral-900 font-medium">{{ amountWithSymbol($order->subtotal) }}</span>
                </div>
                @if ($order->discount > 0)
                    <div class="flex justify-between text-emerald-600 font-medium bg-emerald-50/55 p-2.5 rounded-lg">
                        <span>{{ __('Discount') }} @if ($order->coupon_code)<span class="text-xs text-emerald-500 font-medium">({{ $order->coupon_code }})</span>@endif</span>
                        <span>−{{ amountWithSymbol($order->discount) }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-neutral-600 font-normal">
                    <span>{{ __('Shipping') }}</span>
                    <span class="text-neutral-900 font-medium">{{ $order->shipping_cost > 0 ? amountWithSymbol($order->shipping_cost) : __('Free') }}</span>
                </div>
                <div class="border-t border-neutral-100 pt-4 flex justify-between items-center text-neutral-950 font-bold">
                    <span>{{ __('Total') }}</span>
                    <span class="text-lg sm:text-xl text-brand font-extrabold tracking-tight">{{ amountWithSymbol($order->total) }}</span>
                </div>
            </div>
        </div>

        {{-- Shipping & Customer Details Card --}}
        <div class="bg-white rounded-xl p-5 border border-neutral-100 shadow-[0_2px_12px_rgba(0,0,0,0.015)]">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- Customer Info --}}
                <div class="space-y-3">
                    <h3 class="text-sm font-semibold text-neutral-500 uppercase tracking-wider flex items-center gap-1.5 border-b border-neutral-100 pb-1.5">
                        <i class="ph ph-user text-brand"></i>
                        <span>{{ __('Customer Details') }}</span>
                    </h3>
                    <div class="space-y-2 text-sm sm:text-base">
                        <div class="flex justify-between">
                            <span class="text-neutral-500 font-normal">{{ __('Name') }}</span>
                            <span class="font-medium text-neutral-900">{{ $order->customer_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-neutral-500 font-normal">{{ __('Phone') }}</span>
                            <span class="font-medium text-neutral-900">{{ $order->customer_phone }}</span>
                        </div>
                        @if ($order->customer_email)
                            <div class="flex justify-between items-center gap-4">
                                <span class="text-neutral-500 font-normal shrink-0">{{ __('Email') }}</span>
                                <span class="font-medium text-neutral-900 truncate max-w-[150px]">{{ $order->customer_email }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Shipping Address --}}
                <div class="space-y-3">
                    <h3 class="text-sm font-semibold text-neutral-500 uppercase tracking-wider flex items-center gap-1.5 border-b border-neutral-100 pb-1.5">
                        <i class="ph ph-map-pin text-brand"></i>
                        <span>{{ __('Shipping Address') }}</span>
                    </h3>
                    <div class="text-sm sm:text-base text-neutral-700 leading-relaxed font-normal">
                        <p class="text-neutral-900 font-semibold">{{ $order->shipping_address }}</p>
                        @if ($order->city)
                            <p class="text-neutral-500 mt-0.5 text-xs sm:text-sm uppercase tracking-wider font-semibold">{{ $order->city }} {{ $order->zip_code }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions Panel --}}
        <div class="space-y-3 pt-2">
            @if ($order->isOnlinePayable() && \App\Services\Payment\SslCommerzService::isEnabled())
                <a href="{{ route('shop.payment.sslcommerz.retry', $order->order_number) }}"
                    class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold py-3 px-4 rounded-xl transition duration-150 ease-in-out text-xs sm:text-sm uppercase tracking-wider shadow-[0_4px_12px_rgba(16,185,129,0.2)] hover:shadow-[0_6px_20px_rgba(16,185,129,0.3)] hover:-translate-y-0.5 transform">
                    <i class="ph ph-credit-card text-base"></i>
                    <span>{{ __('PAY NOW') }} — {{ amountWithSymbol($order->total) }}</span>
                </a>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <a href="{{ route('shop.checkout.invoice', $order->order_number) }}" target="_blank"
                    class="flex items-center justify-center gap-1.5 bg-neutral-900 hover:bg-neutral-800 text-white font-bold py-3 px-4 rounded-xl transition duration-150 ease-in-out text-xs sm:text-sm uppercase tracking-wider shadow-[0_2px_8px_rgba(0,0,0,0.05)] hover:-translate-y-0.5 transform text-center">
                    <i class="ph ph-file-text text-sm"></i>
                    <span>{{ __('INVOICE') }}</span>
                </a>

                <a href="{{ route('shop.track', ['order_number' => $order->order_number, 'phone' => $order->customer_phone]) }}"
                    class="flex items-center justify-center gap-1.5 bg-brand hover:bg-brand-dark text-white font-bold py-3 px-4 rounded-xl transition duration-150 ease-in-out text-xs sm:text-sm uppercase tracking-wider shadow-[0_2px_8px_rgba(225,29,72,0.15)] hover:-translate-y-0.5 transform text-center">
                    <i class="ph ph-truck text-sm"></i>
                    <span>{{ __('TRACK LIVE') }}</span>
                </a>

                <a href="{{ route('shop.index') }}"
                    class="flex items-center justify-center gap-1.5 border-2 border-neutral-200 hover:bg-neutral-50 text-neutral-700 font-bold py-3 px-4 rounded-xl transition duration-150 ease-in-out text-xs sm:text-sm uppercase tracking-wider hover:-translate-y-0.5 transform text-center">
                    <i class="ph ph-shopping-bag text-sm"></i>
                    <span>{{ __('SHOP MORE') }}</span>
                </a>
            </div>
        </div>

        <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest text-center pt-2">
            {{ __('WE WILL CALL YOU SHORTLY TO CONFIRM YOUR ORDER.') }}
        </p>
    </div>
</div>
@endsection
