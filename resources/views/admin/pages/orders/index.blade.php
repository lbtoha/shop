<x-admin-app-layout>
    {{-- KPI summary cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 xxl:gap-6 mb-4 xxl:mb-6">
        @php
            $cards = [
                ['label' => __('Total Orders'), 'value' => $stats['total'], 'icon' => 'ph ph-shopping-bag', 'tone' => 'text-primary bg-primary/10'],
                ['label' => __('Pending'), 'value' => $stats['pending'], 'icon' => 'ph ph-clock', 'tone' => 'text-warning bg-warning/10'],
                ['label' => __('Today'), 'value' => $stats['today'], 'icon' => 'ph ph-calendar-check', 'tone' => 'text-info bg-info/10'],
                ['label' => __('Revenue'), 'value' => amountWithSymbol($stats['revenue']), 'icon' => 'ph ph-currency-circle-dollar', 'tone' => 'text-success bg-success/10'],
            ];
        @endphp
        @foreach ($cards as $card)
            <div class="white-box !p-4 flex items-center gap-3">
                <span class="w-12 h-12 rounded-lg flex items-center justify-center text-2xl {{ $card['tone'] }}">
                    <i class="{{ $card['icon'] }}"></i>
                </span>
                <div>
                    <p class="text-xs">{{ $card['label'] }}</p>
                    <p class="m-text font-semibold">{{ $card['value'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="white-box">
        <x-admin::page-header title="{{ __('Orders') }}" :buttons="$buttons" :tab_buttons="$tab_buttons" :isFilterable="true" />
        <x-admin::table :columns="$columns" :data="$orders" />
    </div>
</x-admin-app-layout>
