@extends('shop.layouts.app')

@section('title', __('My Orders') . ' — ' . config('application_info.company_info.name'))

@section('content')
    <div class="shop-container py-8">
        <h1 class="text-2xl font-bold text-ink mb-6">{{ __('My Orders') }}</h1>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            @include('shop.account.partials.sidebar')

            <div class="lg:col-span-3">
                {{-- Flash Message Alert --}}
                @if (session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-4 mb-4 text-sm flex items-center gap-3">
                        <i class="ph ph-check-circle text-xl text-emerald-600"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if (session('error'))
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl p-4 mb-4 text-sm flex items-center gap-3">
                        <i class="ph ph-warning-circle text-xl text-rose-600"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <div class="bg-white border border-neutral-200/80 rounded-2xl overflow-hidden shadow-sm">
                    @if ($orders->isEmpty())
                        <div class="p-8 text-center text-[color:var(--color-muted)]">
                            <i class="ph ph-package text-4xl block mb-2"></i>
                            {{ __('You have no orders yet.') }}
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm min-w-[650px]">
                                <thead class="bg-neutral-600 text-white font-bold text-center">
                                    <tr>
                                        <th class="px-4 py-3.5 text-xs uppercase tracking-wider">{{ __('Order#') }}</th>
                                        <th class="px-4 py-3.5 text-xs uppercase tracking-wider">{{ __('Order Date') }}</th>
                                        <th class="px-4 py-3.5 text-xs uppercase tracking-wider">{{ __('Status') }}</th>
                                        <th class="px-4 py-3.5 text-xs uppercase tracking-wider">{{ __('Total') }}</th>
                                        <th class="px-4 py-3.5 text-xs uppercase tracking-wider">{{ __('action') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-100 text-center">
                                    @foreach ($orders as $order)
                                        <tr class="hover:bg-neutral-50/50 transition-colors">
                                            <td class="px-4 py-4 font-bold text-ink whitespace-nowrap">
                                                {{ __('ID:') }} {{ $order->order_number }}
                                            </td>
                                            <td class="px-4 py-4 text-neutral-500 whitespace-nowrap">
                                                {{ $order->created_at->format('j F Y, g:i A') }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <span class="inline-block text-white text-xs font-bold px-3 py-1 rounded capitalize
                                                    {{ $order->status->value === 'pending' ? 'bg-[#17a2b8]' : '' }}
                                                    {{ $order->status->value === 'confirmed' ? 'bg-blue-500' : '' }}
                                                    {{ $order->status->value === 'processing' ? 'bg-indigo-500' : '' }}
                                                    {{ $order->status->value === 'shipped' ? 'bg-purple-500' : '' }}
                                                    {{ $order->status->value === 'delivered' ? 'bg-emerald-500' : '' }}
                                                    {{ $order->status->value === 'cancelled' ? 'bg-rose-500' : '' }}
                                                ">
                                                    {{ $order->status_name }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 text-ink font-semibold whitespace-nowrap">
                                                {{ (int)$order->total }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap flex items-center justify-center gap-2">
                                                <a href="{{ route('shop.account.order', $order->order_number) }}"
                                                    class="inline-flex items-center gap-1.5 bg-neutral-800 hover:bg-neutral-900 text-white text-xs font-bold py-2 px-4 rounded transition-all">
                                                    <i class="ph ph-eye"></i> {{ __('view') }}
                                                </a>
                                                @if($order->isOnlinePayable() && \App\Services\Payment\SslCommerzService::isEnabled())
                                                    <a href="{{ route('shop.payment.sslcommerz.retry', $order->order_number) }}"
                                                        class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2 px-4 rounded transition-all">
                                                        <i class="ph ph-credit-card"></i> {{ __('Pay') }}
                                                    </a>
                                                @endif
                                                @if($order->status->value === 'pending')
                                                    <form action="{{ route('shop.account.orders.cancel', $order->order_number) }}" method="POST" class="inline" 
                                                        onsubmit="return confirm('{{ __('Are you sure you want to cancel this order?') }}')">
                                                        @csrf
                                                        <button type="submit" 
                                                            class="inline-flex items-center gap-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold py-2 px-4 rounded transition-all">
                                                            <i class="ph ph-trash"></i> {{ __('Cancel') }}
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
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
