@extends('shop.layouts.app')

@section('title', __('My Account') . ' — ' . config('application_info.company_info.name'))

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-ink mb-6">{{ __('My Account') }}</h1>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            @include('shop.account.partials.sidebar')

            <div class="lg:col-span-3 space-y-6">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div class="bg-white border border-neutral-100 rounded p-4">
                        <div class="text-2xl font-bold text-[color:var(--color-brand)]">{{ $orderCount }}</div>
                        <div class="text-sm text-[color:var(--color-muted)]">{{ __('Total Orders') }}</div>
                    </div>
                </div>

                <div class="bg-white border border-neutral-100 rounded">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-neutral-100">
                        <h2 class="font-semibold text-ink">{{ __('Recent Orders') }}</h2>
                        <a href="{{ route('shop.account.orders') }}" class="text-sm text-[color:var(--color-brand)] hover:underline">{{ __('View All') }}</a>
                    </div>

                    @if ($orders->isEmpty())
                        <div class="p-8 text-center text-[color:var(--color-muted)]">
                            <i class="ph ph-package text-4xl block mb-2"></i>
                            {{ __('You have no orders yet.') }}
                            <div class="mt-3"><a href="{{ route('shop.index') }}" class="text-[color:var(--color-brand)] hover:underline">{{ __('Start shopping') }}</a></div>
                        </div>
                    @else
                        <div class="divide-y divide-neutral-100">
                            @foreach ($orders as $order)
                                <a href="{{ route('shop.account.order', $order->order_number) }}" class="flex items-center justify-between px-5 py-3 hover:bg-neutral-50">
                                    <div>
                                        <div class="font-medium text-ink">{{ $order->order_number }}</div>
                                        <div class="text-xs text-[color:var(--color-muted)]">{{ $order->created_at->format('M d, Y') }} · {{ $order->items_count }} {{ __('items') }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-semibold">{{ amountWithSymbol($order->total) }}</div>
                                        <span class="text-xs capitalize text-{{ $order->status->color() === 'success' ? 'emerald' : ($order->status->color() === 'danger' ? 'red' : 'amber') }}-600">{{ $order->status_name }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
