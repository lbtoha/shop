@props([
    'overviews' => [],
])
@php
    $overviewCount = count($overviews);
    if ($overviewCount >= 4) {
        $style = 'grid-cols-1 md:grid-cols-4 gap-4 xxl:gap-6';
    } elseif ($overviewCount >= 3) {
        $style = 'grid-cols-1 md:grid-cols-3 gap-4';
    } elseif ($overviewCount > 2) {
        $style = 'grid-cols-1 md:grid-cols-2 gap-4';
    } else {
        $style = 'grid-cols-1';
    }
@endphp
<div class="grid {{ $style }} mb-4 xl:mb-6">
    @foreach ($overviews as $overview)
        <div class="white-box">
            <div class="flex justify-between items-center gap-3 mb-5">
                <div>
                    <p class="s-text mb-2">{{ $overview['title'] }}</p>
                    <p class="l-text font-semibold">{{ $overview['amount'] }}</p>
                </div>
                <div class="size-11 rounded-full bg-primary f-center">
                    <i class="{{ $overview['icon'] }}"></i>
                </div>
            </div>
            @if (isset($overview['link']))
                <a href="{{ $overview['link'] }}" class="text-blue font-medium text-xs underline">{{ __('View All') }}</a>
            @endif
        </div>
    @endforeach
</div>
