@extends('shop.layouts.app')

@section('title', __('Order Successful') . ' — ' . config('application_info.company_info.name'))

@section('content')
<div class="min-h-[50vh] flex items-center justify-center bg-neutral-20/50 py-12 px-4">
    <div class="max-w-xl w-full bg-white p-8 sm:p-10 rounded-[2rem] border border-neutral-100/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] text-center">
        <div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-neutral-900 tracking-wide uppercase">
                {{ __('ORDER SUCCESSFUL!') }}
            </h2>
            <p class="mt-2 text-xs font-bold text-neutral-400 tracking-widest uppercase">
                {{ __('THANK YOU FOR YOUR PURCHASE') }}
            </p>
        </div>

        <div class="bg-neutral-50 rounded-2xl p-6 my-8 space-y-4 border border-neutral-100/50 text-left">
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
            <a href="{{ route('shop.checkout.invoice', $order->order_number) }}" target="_blank"
                class="w-full flex items-center justify-center gap-2 bg-[#161c24] hover:bg-[#212b36] text-white font-bold py-3.5 px-4 rounded-xl transition duration-150 ease-in-out text-xs uppercase tracking-widest shadow-sm">
                <i class="ph ph-file-text text-base"></i>
                <span>{{ __('DOWNLOAD INVOICE') }}</span>
            </a>

            <a href="{{ route('shop.index') }}"
                class="w-full flex items-center justify-center bg-brand hover:bg-brand-dark text-white font-bold py-3.5 px-4 rounded-xl transition duration-150 ease-in-out text-xs uppercase tracking-widest shadow-sm">
                {{ __('CONTINUE SHOPPING') }}
            </a>
        </div>

        <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest mt-8">
            {{ __('WE WILL CALL YOU SHORTLY TO CONFIRM YOUR ORDER.') }}
        </p>
    </div>
</div>
@endsection
