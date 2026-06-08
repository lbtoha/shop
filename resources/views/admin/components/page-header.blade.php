@props([
    'title' => 'Table',
    'tab_buttons' => [],
    'buttons' => [],
    'isFilterable' => true,
    'dateFilter' => true,
    'customSearchField' => [],
])

<div class="flex justify-between items-center gap-4 flex-wrap mb-4 xl:mb-6">
    @if (isset($tab_buttons) && count($tab_buttons) > 0)
        <div
            class="flex flex-wrap gap-3 p-1 rounded-lg md:rounded-full border border-neutral-30 dark:border-neutral-500 relative">
            @php
                $queryParams = extractQueryParams($tab_buttons);
            @endphp
            @foreach ($tab_buttons as $tab_button)
                <a href="{{ $tab_button['link'] }}"
                    class="relative inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-full
                        {{ isSameUrlForQueryParams(request()->fullUrl(), $tab_button['link'], $queryParams) ? 'text-white bg-primary' : 'text-gray-700 bg-gray-100 hover:bg-gray-200' }}"
                    >
                    {{ $tab_button['label'] }}

                    @if (isset($tab_button['count']) && $tab_button['count'] > 0)
                        <span
                            class="absolute -top-1 right-0 transform translate-x-1/2 -translate-y-1/2 z-50
                     bg-primary text-white text-[10px] font-semibold rounded-full
                     min-w-[1.25rem] h-5 px-1 flex items-center justify-center whitespace-nowrap">
                            {{ $tab_button['count'] }}
                        </span>
                    @endif
                </a>
            @endforeach
        </div>
    @else
        <p class="m-text font-medium">{{ $title }}</p>
    @endif
    <div class="flex gap-4 items-center flex-wrap grow justify-end">
        @if ($isFilterable)
            @php
                $dateRange = request('start_date', '') . ' - ' . request('end_date', '');
                $customDateRange = request('custom_start_date', '') . ' - ' . request('custom_end_date', '');
            @endphp
            <form class="search-form">
                <input type="text" value="{{ request('search') }}" name="search" id="search"
                    placeholder="{{ __('Search') }}" />
                <button type="button" id="table-header-search-btn">
                    <i class="ph ph-magnifying-glass text-lg"></i>
                </button>
            </form>
            @if ($dateFilter)
                <div class="flex items-center gap-3">
                    <span class="text-sm max-md:hidden">{{ __('Date Range') }}: </span>
                    <input type="text" value="{{ $dateRange }}" class="datepicker" placeholder="{{ __('Date Range') }}" id="date-range" />
                </div>
            @endif

            @if (!empty($customSearchField) )
                @foreach ($customSearchField as $searchField)
                    <div class="flex items-center gap-3">
                    <span class="text-sm max-md:hidden">{{ __($searchField['label']) }}: </span>
                    @switch($searchField['type'])
                        @case('daterange')
                            <input type="text" value="{{ $customDateRange }}" class="datepicker" id="custom-date-range" />
                            @break
                        @case('select')
                            <select name="{{ $searchField['name'] }}" id="{{ $searchField['name'] }}">
                                @foreach ($searchField['options'] as $option)
                                    <option value="{{ $option['value'] }}" @selected($option['value'] == request($searchField['name'] ?? ''))>
                                        {{ $option['label'] }}
                                    </option>
                                @endforeach
                            </select>
                            @break
                        @default
                    @endswitch
                </div>
                @endforeach
            @endif
        @endif
        @foreach ($buttons as $button)
            @if ($button['type'] == 'link')
                <a href="{{ $button['link'] }}"
                    @if (isset($button['target'])) target="{{ $button['target'] }}" @endif
                    class="btn-primary outlined {{ isset($button['style']) ? $button['style'] : '' }}">
                    <i class="{{ $button['icon'] }}"></i>
                    <span class="text-xs font-medium">{{ $button['label'] }}</span>
                </a>
            @elseif ($button['type'] == 'modal')
                <button data-modal-target="{{ $button['id'] }}"
                    data-row="{{ isset($button['row']) ? $button['row'] : '' }}" data-action="{{ $button['href'] }}"
                    type="button"
                    class="btn-primary outlined set-create-modal-form-data {{ isset($button['style']) ? $button['style'] : '' }}">
                    <i class="{{ $button['icon'] }}"></i>
                    <span class="text-xs font-medium">{{ $button['label'] }}</span>
                </button>
            @endif
        @endforeach
        @if ($isFilterable)
            <a href="{{ url()->current() }}" class="btn-primary">
                <i class="ph ph-arrows-counter-clockwise text-xs"></i>
            </a>
        @endif
    </div>
</div>
