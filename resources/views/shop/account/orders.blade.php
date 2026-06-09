@extends('shop.layouts.app')

@section('title', __('My Orders') . ' — ' . config('application_info.company_info.name'))

@section('content')
    <div class="shop-container py-8">
        <h1 class="text-2xl font-bold text-ink mb-6">{{ __('My Orders') }}</h1>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            @include('shop.account.partials.sidebar')

            <div class="lg:col-span-3">
                <div class="bg-white border border-neutral-100 rounded overflow-hidden">
                    @if ($orders->isEmpty())
                        <div class="p-8 text-center text-[color:var(--color-muted)]">
                            <i class="ph ph-package text-4xl block mb-2"></i>
                            {{ __('You have no orders yet.') }}
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm min-w-[520px]">
                                <thead class="bg-neutral-50 text-left text-[color:var(--color-muted)]">
                                    <tr>
                                        <th class="px-4 py-3">{{ __('Order') }}</th>
                                        <th class="px-4 py-3">{{ __('Date') }}</th>
                                        <th class="px-4 py-3">{{ __('Items') }}</th>
                                        <th class="px-4 py-3">{{ __('Total') }}</th>
                                        <th class="px-4 py-3">{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-100">
                                    @foreach ($orders as $order)
                                        <tr class="hover:bg-neutral-50">
                                            <td class="px-4 py-3">
                                                <a href="{{ route('shop.account.order', $order->order_number) }}" class="font-medium text-[color:var(--color-brand)] hover:underline">{{ $order->order_number }}</a>
                                            </td>
                                            <td class="px-4 py-3 text-[color:var(--color-muted)] whitespace-nowrap">{{ $order->created_at->format('M d, Y') }}</td>
                                            <td class="px-4 py-3">{{ $order->items_count }}</td>
                                            <td class="px-4 py-3 font-medium whitespace-nowrap">{{ amountWithSymbol($order->total) }}</td>
                                            <td class="px-4 py-3 capitalize">{{ $order->status_name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <div class="mt-6">{{ $orders->links() }}</div>
            </div>
        </div>
    </div>
@endsection
