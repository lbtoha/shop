<x-admin-app-layout>
    {{-- KPI summary cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 xxl:gap-6 mb-4 xxl:mb-6">
        @php
            $cards = [
                ['label' => __('Total Products'), 'value' => $stats['total'], 'icon' => 'ph ph-package', 'tone' => 'text-primary bg-primary/10'],
                ['label' => __('Active'), 'value' => $stats['active'], 'icon' => 'ph ph-check-circle', 'tone' => 'text-success bg-success/10'],
                ['label' => __('Low Stock'), 'value' => $stats['low'], 'icon' => 'ph ph-warning', 'tone' => 'text-warning bg-warning/10'],
                ['label' => __('Out of Stock'), 'value' => $stats['out'], 'icon' => 'ph ph-x-circle', 'tone' => 'text-danger bg-danger/10'],
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
        <x-admin::page-header title="{{ __('Products') }}" :buttons="$buttons" :isFilterable="true" />
        <x-admin::table :columns="$columns" :data="$products" />
    </div>
</x-admin-app-layout>
