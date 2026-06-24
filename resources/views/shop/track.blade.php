@extends('shop.layouts.app')

@section('title', __('Track Your Order') . ' — ' . config('application_info.company_info.name'))

@section('content')
<div class="shop-container py-12">
    <div class="max-w-4xl mx-auto">
        
        {{-- Section Heading --}}
        <div class="text-center mb-10">
            <h1 class="text-3xl font-black text-ink tracking-tight mb-2">
                {{ __('Track Your Order') }}
            </h1>
            <p class="text-sm text-muted max-w-md mx-auto">
                {{ __('Enter your order number and phone number below to check the real-time status of your shipment.') }}
            </p>
        </div>

        {{-- Error Alerts --}}
        @if ($error || session('error'))
            <div class="mb-8 p-4 rounded-2xl bg-rose-50 border border-rose-100 text-rose-800 text-sm flex items-center gap-3">
                <i class="ph ph-warning-circle text-xl shrink-0 text-rose-500"></i>
                <div>
                    {{ $error ?: session('error') }}
                </div>
            </div>
        @endif

        {{-- Tracking Lookup Form --}}
        <div class="bg-white border border-line-soft rounded-md p-6 sm:p-8 mb-8">
            <form action="{{ route('shop.track') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-5 items-end">
                <div>
                    <label for="order_number" class="block text-[11px] font-bold text-ink uppercase tracking-wider mb-2">
                        {{ __('Order Number') }} <span class="text-brand">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-neutral-400">
                            <i class="ph ph-hash"></i>
                        </div>
                        <input type="text" 
                               name="order_number" 
                               id="order_number" 
                               required 
                               value="{{ request('order_number') }}" 
                               placeholder="e.g. 20260623-123456" 
                               class="w-full pl-9 pr-4 py-3 bg-neutral-50/50 border border-neutral-200/80 rounded-md text-sm focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand-soft transition-all text-ink font-semibold placeholder:font-normal">
                    </div>
                </div>

                <div>
                    <label for="phone" class="block text-[11px] font-bold text-ink uppercase tracking-wider mb-2">
                        {{ __('Billing Phone') }} <span class="text-brand">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-neutral-400">
                            <i class="ph ph-phone"></i>
                        </div>
                        <input type="text" 
                               name="phone" 
                               id="phone" 
                               required 
                               value="{{ request('phone') }}" 
                               placeholder="e.g. 017XXXXXXXX" 
                               class="w-full pl-9 pr-4 py-3 bg-neutral-50/50 border border-neutral-200/80 rounded-md text-sm focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand-soft transition-all text-ink font-semibold placeholder:font-normal">
                    </div>
                </div>

                <div>
                    <button type="submit" 
                            class="w-full bg-brand hover:bg-brand-dark text-white font-bold py-3.5 px-6 rounded-md text-xs tracking-wider uppercase flex items-center justify-center gap-2 transition-all duration-250 cursor-pointer">
                        <i class="ph ph-magnifying-glass text-sm"></i>
                        <span>{{ __('Track Now') }}</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Tracking Results --}}
        @if ($searched && $order)
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

            <div class="space-y-6">
                
                {{-- Order status indicator & timeline --}}
                <div class="bg-white border border-line-soft rounded-md p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-line-soft mb-8">
                        <div>
                            <span class="text-[11px] font-bold text-neutral-400 uppercase tracking-widest block mb-1">
                                {{ __('Order Details') }}
                            </span>
                            <div class="flex items-center gap-2">
                                <h3 class="text-xl font-bold text-ink">
                                    {{ __('Order') }} <span class="text-brand">#{{ $order->order_number }}</span>
                                </h3>
                                @if($isCancelled)
                                    <span class="bg-rose-50 text-rose-600 border border-rose-100 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded">
                                        {{ __('Cancelled') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="text-sm text-right sm:text-right text-left">
                            <span class="text-xs text-muted block mb-0.5">{{ __('Placed on') }}</span>
                            <span class="font-bold text-ink">{{ $order->created_at->format('d M, Y g:i A') }}</span>
                        </div>
                    </div>

                    @if($isCancelled)
                        <div class="p-6 rounded-2xl bg-rose-50/50 border border-rose-100 text-center">
                            <div class="w-12 h-12 bg-rose-100 rounded-full flex items-center justify-center mx-auto mb-3 text-rose-600">
                                <i class="ph ph-x-circle text-2xl font-bold"></i>
                            </div>
                            <h4 class="text-base font-extrabold text-rose-800 mb-1">{{ __('This order is Cancelled') }}</h4>
                            <p class="text-xs text-rose-600 max-w-md mx-auto">
                                {{ __('This order has been cancelled and is no longer being processed. Please contact customer support if you believe this is an error.') }}
                            </p>
                        </div>
                    @else
                        {{-- Visual Timeline (Desktop) --}}
                        <div class="hidden md:block py-4">
                            <div class="relative">
                                {{-- Line & Circles --}}
                                <div class="relative flex items-center justify-between h-10">
                                    {{-- Track Background Line --}}
                                    <div class="absolute left-[10%] right-[10%] top-1/2 -translate-y-1/2 h-1 bg-neutral-100 rounded-full -z-0"></div>
                                    <div class="absolute left-[10%] top-1/2 -translate-y-1/2 h-1 bg-brand rounded-full transition-all duration-500 -z-0"
                                         style="width: {{ (($currentStep - 1) / 4) * 80 }}%"></div>

                                    {{-- Step 1: Placed --}}
                                    <div class="relative z-10 flex-1 flex justify-center">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300
                                            {{ $currentStep >= 1 ? 'bg-brand text-white' : 'bg-white border-2 border-neutral-200 text-neutral-400' }}
                                            {{ $currentStep === 1 ? 'ring-4 ring-brand-soft animate-pulse' : '' }}">
                                            <i class="ph ph-receipt"></i>
                                        </div>
                                    </div>

                                    {{-- Step 2: Confirmed --}}
                                    <div class="relative z-10 flex-1 flex justify-center">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300
                                            {{ $currentStep >= 2 ? 'bg-brand text-white' : 'bg-white border-2 border-neutral-200 text-neutral-400' }}
                                            {{ $currentStep === 2 ? 'ring-4 ring-brand-soft animate-pulse' : '' }}">
                                            <i class="ph ph-thumbs-up"></i>
                                        </div>
                                    </div>

                                    {{-- Step 3: Processing --}}
                                    <div class="relative z-10 flex-1 flex justify-center">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300
                                            {{ $currentStep >= 3 ? 'bg-brand text-white' : 'bg-white border-2 border-neutral-200 text-neutral-400' }}
                                            {{ $currentStep === 3 ? 'ring-4 ring-brand-soft animate-pulse' : '' }}">
                                            <i class="ph ph-gear-six"></i>
                                        </div>
                                    </div>

                                    {{-- Step 4: Shipped --}}
                                    <div class="relative z-10 flex-1 flex justify-center">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300
                                            {{ $currentStep >= 4 ? 'bg-brand text-white' : 'bg-white border-2 border-neutral-200 text-neutral-400' }}
                                            {{ $currentStep === 4 ? 'ring-4 ring-brand-soft animate-pulse' : '' }}">
                                            <i class="ph ph-truck"></i>
                                        </div>
                                    </div>

                                    {{-- Step 5: Delivered --}}
                                    <div class="relative z-10 flex-1 flex justify-center">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300
                                            {{ $currentStep >= 5 ? 'bg-brand text-white' : 'bg-white border-2 border-neutral-200 text-neutral-400' }}
                                            {{ $currentStep === 5 ? 'ring-4 ring-brand-soft animate-pulse' : '' }}">
                                            <i class="ph ph-house-line"></i>
                                        </div>
                                    </div>
                                </div>

                                {{-- Labels --}}
                                <div class="flex justify-between mt-3">
                                    <div class="flex-1 text-center text-xs font-bold {{ $currentStep >= 1 ? 'text-ink' : 'text-neutral-400' }}">{{ __('Placed') }}</div>
                                    <div class="flex-1 text-center text-xs font-bold {{ $currentStep >= 2 ? 'text-ink' : 'text-neutral-400' }}">{{ __('Confirmed') }}</div>
                                    <div class="flex-1 text-center text-xs font-bold {{ $currentStep >= 3 ? 'text-ink' : 'text-neutral-400' }}">{{ __('Processing') }}</div>
                                    <div class="flex-1 text-center text-xs font-bold {{ $currentStep >= 4 ? 'text-ink' : 'text-neutral-400' }}">{{ __('Shipped') }}</div>
                                    <div class="flex-1 text-center text-xs font-bold {{ $currentStep >= 5 ? 'text-ink' : 'text-neutral-400' }}">{{ __('Delivered') }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Visual Timeline (Mobile) --}}
                        <div class="block md:hidden space-y-6 relative pl-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-neutral-100">
                            
                            {{-- Step 1 --}}
                            <div class="relative flex items-start gap-4">
                                <div class="absolute -left-[22px] w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold transition-all duration-300
                                    {{ $currentStep >= 1 ? 'bg-brand text-white' : 'bg-white border-2 border-neutral-200 text-neutral-400' }}">
                                    @if($currentStep > 1) <i class="ph ph-check"></i> @else 1 @endif
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold {{ $currentStep >= 1 ? 'text-ink' : 'text-neutral-400' }}">{{ __('Order Placed') }}</h4>
                                    <p class="text-xs text-muted">{{ __('We have received your order confirmation.') }}</p>
                                </div>
                            </div>

                            {{-- Step 2 --}}
                            <div class="relative flex items-start gap-4">
                                <div class="absolute -left-[22px] w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold transition-all duration-300
                                    {{ $currentStep >= 2 ? 'bg-brand text-white' : 'bg-white border-2 border-neutral-200 text-neutral-400' }}">
                                    @if($currentStep > 2) <i class="ph ph-check"></i> @else 2 @endif
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold {{ $currentStep >= 2 ? 'text-ink' : 'text-neutral-400' }}">{{ __('Order Confirmed') }}</h4>
                                    <p class="text-xs text-muted">{{ __('Your order details and stock have been validated.') }}</p>
                                </div>
                            </div>

                            {{-- Step 3 --}}
                            <div class="relative flex items-start gap-4">
                                <div class="absolute -left-[22px] w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold transition-all duration-300
                                    {{ $currentStep >= 3 ? 'bg-brand text-white' : 'bg-white border-2 border-neutral-200 text-neutral-400' }}">
                                    @if($currentStep > 3) <i class="ph ph-check"></i> @else 3 @endif
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold {{ $currentStep >= 3 ? 'text-ink' : 'text-neutral-400' }}">{{ __('Processing / Packing') }}</h4>
                                    <p class="text-xs text-muted">{{ __('We are preparing your parcel for dispatch.') }}</p>
                                </div>
                            </div>

                            {{-- Step 4 --}}
                            <div class="relative flex items-start gap-4">
                                <div class="absolute -left-[22px] w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold transition-all duration-300
                                    {{ $currentStep >= 4 ? 'bg-brand text-white' : 'bg-white border-2 border-neutral-200 text-neutral-400' }}">
                                    @if($currentStep > 4) <i class="ph ph-check"></i> @else 4 @endif
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold {{ $currentStep >= 4 ? 'text-ink' : 'text-neutral-400' }}">{{ __('Shipped') }}</h4>
                                    <p class="text-xs text-muted">{{ __('Your parcel is with the courier service on its way.') }}</p>
                                </div>
                            </div>

                            {{-- Step 5 --}}
                            <div class="relative flex items-start gap-4">
                                <div class="absolute -left-[22px] w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold transition-all duration-300
                                    {{ $currentStep >= 5 ? 'bg-brand text-white' : 'bg-white border-2 border-neutral-200 text-neutral-400' }}">
                                    @if($currentStep > 5) <i class="ph ph-check"></i> @else 5 @endif
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold {{ $currentStep >= 5 ? 'text-ink' : 'text-neutral-400' }}">{{ __('Delivered') }}</h4>
                                    <p class="text-xs text-muted">{{ __('Package successfully delivered to your doorstep.') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Courier Integration Box --}}
                @if ($order->courier_tracking_code)
                    <div class="bg-gradient-to-r from-neutral-900 to-neutral-800 text-white rounded-md p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 border border-neutral-800">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="bg-brand text-white text-[9px] font-bold tracking-widest uppercase px-2.5 py-0.5 rounded-full">
                                    {{ __('COURIER DISPATCH') }}
                                </span>
                            </div>
                            <h4 class="text-lg font-bold">
                                {{ __('Shipped via Steadfast Courier') }}
                            </h4>
                            <p class="text-xs text-neutral-400 leading-relaxed">
                                {{ __('Your order is being handled by Steadfast. You can track this package directly on the courier portal using the tracking number below.') }}
                            </p>
                            <div class="flex flex-wrap gap-4 pt-2 text-xs">
                                <div>
                                    <span class="text-neutral-500 font-bold block">{{ __('TRACKING NUMBER') }}</span>
                                    <code class="text-sm text-brand font-mono font-bold">{{ $order->courier_tracking_code }}</code>
                                </div>
                                @if($order->courier_status)
                                    <div>
                                        <span class="text-neutral-500 font-bold block">{{ __('COURIER STATUS') }}</span>
                                        <span class="text-white font-bold capitalize">{{ str_replace('_', ' ', $order->courier_status) }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="shrink-0 w-full sm:w-auto">
                            <a href="https://steadfast.com.bd/tracking" 
                               target="_blank" 
                               rel="noopener" 
                               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-brand hover:bg-brand-dark text-white text-xs font-bold py-3.5 px-6 rounded-md transition-all">
                                <i class="ph ph-arrow-square-out text-sm"></i>
                                <span>{{ __('Track on Steadfast') }}</span>
                            </a>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Customer details & address --}}
                    <div class="bg-white border border-line-soft rounded-md p-6 md:col-span-1 space-y-4">
                        <h4 class="text-sm font-bold text-ink pb-3 border-b border-line-soft flex items-center gap-2">
                            <i class="ph ph-user text-brand text-lg"></i>
                            {{ __('Delivery Details') }}
                        </h4>
                        <div class="text-xs space-y-3">
                            <div>
                                <span class="text-neutral-400 font-bold block mb-0.5">{{ __('RECIPIENT NAME') }}</span>
                                <span class="font-bold text-ink text-sm">{{ $order->customer_name }}</span>
                            </div>
                            <div>
                                <span class="text-neutral-400 font-bold block mb-0.5">{{ __('CONTACT PHONE') }}</span>
                                <span class="font-semibold text-neutral-800">{{ $order->customer_phone }}</span>
                            </div>
                            @if ($order->customer_email)
                                <div>
                                    <span class="text-neutral-400 font-bold block mb-0.5">{{ __('EMAIL ADDRESS') }}</span>
                                    <span class="font-semibold text-neutral-800">{{ $order->customer_email }}</span>
                                </div>
                            @endif
                            <div>
                                <span class="text-neutral-400 font-bold block mb-0.5">{{ __('SHIPPING ADDRESS') }}</span>
                                <span class="font-semibold text-neutral-800 leading-relaxed block">
                                    {{ $order->shipping_address }}@if($order->city), {{ $order->city }}@endif {{ $order->zip_code }}
                                </span>
                            </div>
                            @if($order->note)
                                <div class="bg-neutral-50 p-3 rounded-md border border-neutral-100/80">
                                    <span class="text-neutral-400 font-bold block mb-1">{{ __('ORDER NOTE') }}</span>
                                    <span class="text-neutral-700 italic block">{{ $order->note }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Items summary --}}
                    <div class="bg-white border border-line-soft rounded-md p-6 md:col-span-2 space-y-4">
                        <h4 class="text-sm font-bold text-ink pb-3 border-b border-line-soft flex items-center gap-2">
                            <i class="ph ph-shopping-bag text-brand text-lg"></i>
                            {{ __('Ordered Items') }}
                        </h4>
                        
                        <div class="divide-y divide-neutral-100 text-xs">
                            @foreach ($order->items as $item)
                                @php
                                    $product = $item->product;
                                    $imageUrl = $product 
                                        ? ($product->thumbnail ?: ($product->images->isNotEmpty() ? $product->images->first()->image : null))
                                        : null;
                                    $hasLink = $product && $product->slug;
                                @endphp
                                <div class="flex items-center gap-3.5 py-3 first:pt-0 last:pb-0">
                                    {{-- Thumbnail image --}}
                                    <div class="w-12 h-12 bg-neutral-50 border border-neutral-100 rounded-md overflow-hidden shrink-0 flex items-center justify-center">
                                        @if($imageUrl)
                                            <img src="{{ $imageUrl }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                        @else
                                            <i class="ph ph-image text-xl text-neutral-300"></i>
                                        @endif
                                    </div>
                                    
                                    {{-- Info --}}
                                    <div class="flex-1 min-w-0">
                                        <h5 class="font-bold text-ink truncate hover:text-brand transition-colors">
                                            @if($hasLink)
                                                <a href="{{ route('shop.product', $product->slug) }}">{{ $item->product_name }}</a>
                                            @else
                                                {{ $item->product_name }}
                                            @endif
                                        </h5>
                                        @if($item->variant)
                                            <span class="inline-block mt-0.5 px-1.5 py-0.5 bg-neutral-100 rounded text-[9px] font-bold uppercase tracking-wider text-neutral-500">
                                                {{ $item->variant->name }}
                                            </span>
                                        @endif
                                        <div class="mt-1 text-[11px] text-muted font-medium">
                                            <span>{{ __('Qty:') }}</span> <span class="font-bold text-neutral-800">{{ $item->quantity }}</span>
                                            <span class="mx-1">×</span>
                                            <span>{{ amountWithSymbol($item->price) }}</span>
                                        </div>
                                    </div>

                                    {{-- Total --}}
                                    <div class="text-right font-bold text-ink whitespace-nowrap">
                                        {{ amountWithSymbol($item->subtotal) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Calculation Breakdown --}}
                        <div class="border-t border-line-soft pt-4 space-y-2 text-xs">
                            <div class="flex justify-between items-center text-neutral-600">
                                <span>{{ __('Subtotal') }}</span>
                                <span class="font-semibold">{{ amountWithSymbol($order->subtotal) }}</span>
                            </div>
                            @if ($order->discount > 0)
                                <div class="flex justify-between items-center text-emerald-600 font-bold">
                                    <span>{{ __('Discount') }} @if($order->coupon_code)<span class="text-[9px]">({{ $order->coupon_code }})</span>@endif</span>
                                    <span>-{{ amountWithSymbol($order->discount) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between items-center text-neutral-600">
                                <span>{{ __('Shipping Cost') }}</span>
                                <span class="font-semibold">
                                    {{ $order->shipping_cost > 0 ? amountWithSymbol($order->shipping_cost) : __('Free') }}
                                </span>
                            </div>
                            <div class="border-t border-line-soft pt-3 flex justify-between items-center font-bold text-ink text-sm">
                                <span>{{ __('Grand Total') }}</span>
                                <span class="text-base text-brand font-black">{{ amountWithSymbol($order->total) }}</span>
                            </div>
                        </div>

                        {{-- Payment retry option --}}
                        @if ($order->isOnlinePayable() && \App\Services\Payment\SslCommerzService::isEnabled())
                            <div class="pt-3 border-t border-line-soft">
                                <a href="{{ route('shop.payment.sslcommerz.retry', $order->order_number) }}"
                                   class="w-full flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-3.5 px-4 rounded-md transition duration-150">
                                    <i class="ph ph-credit-card text-base"></i>
                                    <span>{{ __('PAY NOW') }} — {{ amountWithSymbol($order->total) }}</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

        @elseif ($searched)
            {{-- Search attempted but not found --}}
            <div class="bg-white border border-line-soft rounded-md p-8 text-center">
                <div class="w-14 h-14 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-4 text-rose-500 border border-rose-100">
                    <i class="ph ph-magnifying-glass-plus text-2xl font-bold"></i>
                </div>
                <h3 class="text-lg font-black text-ink mb-1.5">{{ __('Order Not Found') }}</h3>
                <p class="text-xs text-muted max-w-sm mx-auto mb-2">
                    {{ __('We could not find any order matching that Order Number and Phone Number in our records.') }}
                </p>
                <p class="text-[11px] text-brand font-bold uppercase tracking-wider">
                    {{ __('Please re-check your inputs and try again.') }}
                </p>
            </div>
        @endif

    </div>
</div>
@endsection
