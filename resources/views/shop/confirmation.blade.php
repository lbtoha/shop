@extends('shop.layouts.app')
@section('title', __('Order Successful') . ' — ' . config('application_info.company_info.name'))

@section('content')
<div class="min-h-[60vh] flex items-center justify-center bg-neutral-50/50 py-8 sm:py-12 px-3 sm:px-4">
    <div class="max-w-xl w-full bg-white p-5 sm:p-8 md:p-10 rounded-2xl border border-neutral-100 shadow-[0_10px_30px_rgba(0,0,0,0.03)] text-center">
        
        {{-- Success Checkmark Badge --}}
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-50 text-emerald-500 mb-5 relative">
            <span class="absolute inset-0 rounded-full bg-emerald-500/15 animate-ping duration-1000"></span>
            <i class="ph-fill ph-check-circle text-4xl"></i>
        </div>

        <div>
            <h2 class="text-2xl sm:text-3xl font-black text-neutral-900 tracking-wide uppercase">
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
        <div class="bg-neutral-50/50 rounded-xl p-4 sm:p-6 my-6 border border-neutral-100 text-left">
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
                                    {{ $currentStep >= 1 ? 'bg-brand text-white shadow-sm shadow-brand/30' : 'bg-white border border-neutral-200 text-neutral-400' }}
                                    {{ $currentStep === 1 ? 'ring-4 ring-brand-soft animate-pulse' : '' }}">
                                    <i class="ph ph-receipt"></i>
                                </div>
                            </div>

                            {{-- Step 2: Confirmed --}}
                            <div class="relative z-10 flex-1 flex justify-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300
                                    {{ $currentStep >= 2 ? 'bg-brand text-white shadow-sm shadow-brand/30' : 'bg-white border border-neutral-200 text-neutral-400' }}
                                    {{ $currentStep === 2 ? 'ring-4 ring-brand-soft animate-pulse' : '' }}">
                                    <i class="ph ph-thumbs-up"></i>
                                </div>
                            </div>

                            {{-- Step 3: Processing --}}
                            <div class="relative z-10 flex-1 flex justify-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300
                                    {{ $currentStep >= 3 ? 'bg-brand text-white shadow-sm shadow-brand/30' : 'bg-white border border-neutral-200 text-neutral-400' }}
                                    {{ $currentStep === 3 ? 'ring-4 ring-brand-soft animate-pulse' : '' }}">
                                    <i class="ph ph-gear-six"></i>
                                </div>
                            </div>

                            {{-- Step 4: Shipped --}}
                            <div class="relative z-10 flex-1 flex justify-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300
                                    {{ $currentStep >= 4 ? 'bg-brand text-white shadow-sm shadow-brand/30' : 'bg-white border border-neutral-200 text-neutral-400' }}
                                    {{ $currentStep === 4 ? 'ring-4 ring-brand-soft animate-pulse' : '' }}">
                                    <i class="ph ph-truck"></i>
                                </div>
                            </div>

                            {{-- Step 5: Delivered --}}
                            <div class="relative z-10 flex-1 flex justify-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300
                                    {{ $currentStep >= 5 ? 'bg-brand text-white shadow-sm shadow-brand/30' : 'bg-white border border-neutral-200 text-neutral-400' }}
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
                <div class="block md:hidden space-y-4 relative pl-5 before:absolute before:left-1.5 before:top-1 before:bottom-1 before:w-0.5 before:bg-neutral-200">
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

        {{-- Order Summary Card --}}
        <div class="bg-white rounded-xl p-4 sm:p-6 my-6 space-y-4 border border-neutral-100 shadow-[0_4px_15px_rgba(0,0,0,0.015)] text-left">
            <div class="flex justify-between items-center text-sm">
                <span class="text-xs font-bold text-neutral-400 uppercase tracking-wider">{{ __('ORDER NUMBER') }}</span>
                <span class="font-extrabold text-brand bg-brand/5 px-3 py-1.5 rounded-full text-xs tracking-wide">{{ $order->order_number }}</span>
            </div>
            <div class="border-t border-neutral-100 my-2"></div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs pt-1">
                <div class="space-y-1">
                    <span class="font-bold text-neutral-400 uppercase tracking-wider block mb-1.5">{{ __('Customer Details') }}</span>
                    <div class="font-bold text-neutral-800 text-sm flex items-center gap-1.5">
                        <i class="ph ph-user text-neutral-400"></i>
                        <span>{{ $order->customer_name }}</span>
                    </div>
                    <div class="text-neutral-600 font-medium flex items-center gap-1.5">
                        <i class="ph ph-phone text-neutral-400"></i>
                        <span>{{ $order->customer_phone }}</span>
                    </div>
                    @if ($order->customer_email)
                        <div class="text-neutral-500 flex items-center gap-1.5">
                            <i class="ph ph-envelope text-neutral-400"></i>
                            <span>{{ $order->customer_email }}</span>
                        </div>
                    @endif
                </div>
                <div class="space-y-1">
                    <span class="font-bold text-neutral-400 uppercase tracking-wider block mb-1.5">{{ __('Shipping Address') }}</span>
                    <div class="text-neutral-600 font-medium leading-relaxed flex items-start gap-1.5">
                        <i class="ph ph-map-pin text-neutral-400 mt-0.5"></i>
                        <span>
                            {{ $order->shipping_address }}
                            @if ($order->city)
                                <br><span class="text-neutral-500">{{ $order->city }} {{ $order->zip_code }}</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <div class="border-t border-neutral-100 my-2"></div>
            
            <div class="space-y-3 pt-1">
                <span class="font-bold text-neutral-400 uppercase tracking-wider block mb-1.5">{{ __('Order Items') }}</span>
                <div class="divide-y divide-neutral-100">
                    @foreach ($order->items as $item)
                        <div class="flex justify-between py-2.5 text-xs font-medium text-neutral-700">
                            <span class="flex items-center gap-1.5 pr-4">
                                <i class="ph ph-dot text-brand text-lg leading-none"></i>
                                <span class="line-clamp-1">{{ $item->product_name }}</span>
                                @if ($item->variant_name)
                                    <span class="text-brand font-bold shrink-0">({{ $item->variant_name }})</span>
                                @endif
                                <span class="text-neutral-400 font-bold shrink-0">×{{ $item->quantity }}</span>
                            </span>
                            <span class="font-semibold text-neutral-800 shrink-0">{{ amountWithSymbol($item->subtotal) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-neutral-100 my-2"></div>

            <div class="space-y-2 text-xs pt-1">
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
                <div class="border-t border-neutral-100 pt-3 flex justify-between items-center text-sm font-extrabold text-neutral-900">
                    <span>{{ __('TOTAL AMOUNT') }}</span>
                    <span class="text-xl text-brand font-black">{{ amountWithSymbol($order->total) }}</span>
                </div>
            </div>
        </div>

        {{-- Premium Action Buttons --}}
        <div class="space-y-3 pt-2">
            @if ($order->isOnlinePayable() && \App\Services\Payment\SslCommerzService::isEnabled())
                <a href="{{ route('shop.payment.sslcommerz.retry', $order->order_number) }}"
                    class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold py-3.5 px-4 rounded-xl transition duration-150 ease-in-out text-xs uppercase tracking-widest shadow-[0_4px_12px_rgba(16,185,129,0.2)] hover:shadow-[0_6px_20px_rgba(16,185,129,0.3)] hover:-translate-y-0.5 transform">
                    <i class="ph ph-credit-card text-base"></i>
                    <span>{{ __('PAY NOW') }} — {{ amountWithSymbol($order->total) }}</span>
                </a>
            @endif

            <a href="{{ route('shop.checkout.invoice', $order->order_number) }}" target="_blank"
                class="w-full flex items-center justify-center gap-2 bg-neutral-900 hover:bg-neutral-800 text-white font-bold py-3.5 px-4 rounded-xl transition duration-150 ease-in-out text-xs uppercase tracking-widest shadow-[0_4px_12px_rgba(0,0,0,0.06)] hover:shadow-[0_6px_20px_rgba(0,0,0,0.12)] hover:-translate-y-0.5 transform">
                <i class="ph ph-file-text text-base"></i>
                <span>{{ __('DOWNLOAD INVOICE') }}</span>
            </a>

            <a href="{{ route('shop.track', ['order_number' => $order->order_number, 'phone' => $order->customer_phone]) }}"
                class="w-full flex items-center justify-center gap-2 bg-brand hover:bg-brand-dark text-white font-bold py-3.5 px-4 rounded-xl transition duration-150 ease-in-out text-xs uppercase tracking-widest shadow-[0_4px_12px_rgba(225,29,72,0.2)] hover:shadow-[0_6px_20px_rgba(225,29,72,0.3)] hover:-translate-y-0.5 transform">
                <i class="ph ph-truck text-base"></i>
                <span>{{ __('TRACK ORDER LIVE') }}</span>
            </a>

            <a href="{{ route('shop.index') }}"
                class="w-full flex items-center justify-center gap-2 border border-neutral-200 hover:bg-neutral-50 text-neutral-700 font-bold py-3.5 px-4 rounded-xl transition duration-150 ease-in-out text-xs uppercase tracking-widest hover:-translate-y-0.5 transform">
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
