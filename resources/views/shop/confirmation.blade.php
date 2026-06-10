@extends('shop.layouts.app')

@section('title', __('Order Successful') . ' — ' . config('application_info.company_info.name'))

@section('content')
<div class="min-h-[50vh] flex items-center justify-center bg-neutral-20/50 py-12 px-4">
    <div class="max-w-md w-full bg-white p-8 sm:p-10 rounded-[2rem] border border-neutral-100/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] text-center">
        <div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-neutral-900 tracking-wide uppercase">
                {{ __('ORDER SUCCESSFUL!') }}
            </h2>
            <p class="mt-2 text-xs font-bold text-neutral-400 tracking-widest uppercase">
                {{ __('THANK YOU FOR YOUR PURCHASE') }}
            </p>
        </div>

        <div class="bg-neutral-50 rounded-2xl p-6 my-8 space-y-4 border border-neutral-100/50">
            <div class="flex justify-between items-center text-sm">
                <span class="text-xs font-bold text-neutral-400 uppercase tracking-wider">{{ __('ORDER NUMBER') }}</span>
                <span class="font-extrabold text-neutral-900 tracking-wide">{{ $order->order_number }}</span>
            </div>
            <div class="border-t border-neutral-200/60 my-2"></div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-xs font-bold text-neutral-400 uppercase tracking-wider">{{ __('TOTAL AMOUNT') }}</span>
                <span class="text-xl font-extrabold text-brand">{{ amountWithSymbol($order->total) }}</span>
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
