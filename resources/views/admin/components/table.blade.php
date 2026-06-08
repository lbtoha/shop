@props([
    'columns' => [],
    'data' => [],
    'enableCheckbox' => false,
    'is_pagination' => true,
    'emptyMessage' => __('Data not found!'),
    'checkboxName' => 'ids',
])

<div class="overflow-x-auto">
    <table class="w-full responsive">
        <thead>
            <tr class="bg-primary/5 border-b border-neutral-30 dark:border-neutral-500">
                @if ($enableCheckbox)
                    <th class="p-4 w-10">
                        <div class="table-header">
                            <input type="checkbox"
                                class="select-all-checkbox form-checkbox h-5 w-5 text-primary rounded border-neutral-30 dark:border-neutral-500">
                        </div>
                    </th>
                @endif
                @foreach ($columns as $column)
                    <th
                        class="p-4 {{ count($columns) == $loop->index + 1 ? 'flex justify-end' : 'text-left' }} @isset($column['header_class']) {{ $column['header_class'] }} @endisset">
                        <div class="table-header ">
                            {{ $column['label'] ?? '' }}

                            @if (isset($column['is_sortable']) && $column['is_sortable'])
                                <div class="flex flex-col items-center">
                                    @if (request('sort') == 'asc' && request('column') == $column['key'])
                                        <a href="{{ url(request()->fullUrlWithQuery(['sort' => 'desc', 'column' => $column['key']])) }}"
                                            class="f-center cursor-pointer">
                                            <i class="ph ph-caret-up text-lg text-green-600 !text-bold"></i>
                                        </a>
                                    @elseif (request('sort') == 'desc' && request('column') == $column['key'])
                                        <a href="{{ url(request()->fullUrlWithQuery(['sort' => 'asc', 'column' => $column['key']])) }}"
                                            class="f-center  cursor-pointer">
                                            <i class="ph ph-caret-down text-lg text-green-600 text-bold"></i>
                                        </a>
                                    @else
                                        <a href="{{ url(request()->fullUrlWithQuery(['sort' => 'asc', 'column' => $column['key']])) }}"
                                            class="f-center  cursor-pointer">
                                            <i class="ph ph-caret-down text-lg text-green-600 text-bold"></i>
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $key => $row)
                <tr class="border-b border-neutral-30 dark:border-neutral-500 hover:bg-primary/5 duration-300">
                    @if ($enableCheckbox)
                        <td class="p-4">
                            <input type="checkbox" name="{{ $checkboxName }}[{{ $key }}]" value="{{ $row->id }}"
                                {{ isset($row->disabled) && $row->disabled ? 'disabled' : '' }}
                                class="table-row-checkbox form-checkbox h-5 w-5 rounded border-neutral-30 dark:border-neutral-500 {{ isset($row->disabled) && $row->disabled ? 'text-gray-400 cursor-not-allowed opacity-50' : 'text-primary' }}">
                        </td>
                    @endif
                    @foreach ($columns as $column)
                        <td class="p-4 {{ count($columns) == $loop->index + 1 ? 'flex justify-end' : 'text-left max-lg:flex! justify-between items-center' }} {{ isset($column['row_class']) ? $column['row_class'] : '' }}"
                            data-th="{{ $column['label'] ?? '' }}">
                            @if (isset($column['render']) && is_callable($column['render']))
                                {!! $column['render']($row) !!}
                            @else
                                {{ isset($column['key']) ? getValueFromArray($row, $column['key']) : '' }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr class="w-full h-[300px]">
                    <td class="text-center" colspan="{{ count($columns) }}">{{ $emptyMessage }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @if (!empty($data) && $is_pagination)
        @php
            $totalData = $data->total();
            $totalPages = $data->lastPage();
            $perPage = $data->perPage();
            $currentPage = $data->currentPage();
            $query = request()->query();
            $currentData = $totalData > 0 ? $perPage * ($currentPage - 1) + 1 : 0;
            $currentPageLast = $totalData > 0 ? min($totalData, $currentData + $perPage - 1) : 0;
        @endphp
        <div class="flex col-span-12 gap-4 sm:justify-between justify-center items-center flex-wrap mt-6">
            <!-- Showing Entries Section -->
            <div class="flex items-center max-sm:justify-center flex-wrap gap-3">
                <p>{{ __('Showing :data to :currentPage of :total entries', [
                    'data' => $currentData,
                    'currentPage' => $currentPageLast,
                    'total' => $totalData,
                ]) }}
                </p>
                <div class="flex gap-4 items-center">
                    <p>{{ __('Per page') }}:</p>
                    <select id="perPageAction" name="rows" class="bg-neutral-0 dark:bg-neutral-904"
                        onchange="window.location.href = '{{ request()->urlWithQuery }}?rows=' + this.value">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
            </div>
            <!-- Pagination Section -->
            <ul class="flex gap-2 md:gap-3 flex-wrap md:font-semibold items-center">
                <!-- First Page -->
                <li>
                    <a href="{{ $data->onFirstPage() ? '#' : getUrlWithQuery($data->url(1), $query) }}"
                        class="pagination-btn {{ $data->onFirstPage() ? 'bg-primary/20 cursor-not-allowed' : '' }}"
                        {{ $data->onFirstPage() ? 'aria-disabled=true' : '' }}>
                        <i class="ph ph-caret-left text-lg"></i>
                    </a>
                </li>
                <!-- Page Numbers -->
                @for ($index = 1; $index <= $totalPages; $index++)
                    @if ($index == $currentPage || ($index >= $currentPage - 2 && $index <= $currentPage + 2))
                        <li>
                            <a href="{{ $index == $currentPage ? '#' : getUrlWithQuery($data->url($index), $query) }}"
                                class="pagination-btn {{ $index == $currentPage ? 'active' : '' }}">
                                {{ $index }}
                            </a>
                        </li>
                    @elseif ($index == 1 || $index == $totalPages)
                        <li>
                            <a href="{{ getUrlWithQuery($data->url($index), $query) }}" class="pagination-btn">
                                {{ $index }}
                            </a>
                        </li>
                    @elseif ($index == $currentPage - 3 || $index == $currentPage + 3)
                        <li>
                            <span class="pagination-dots">...</span>
                        </li>
                    @endif
                @endfor
                <!-- Last Page -->
                <li>
                    <a href="{{ $data->hasMorePages() ? getUrlWithQuery($data->url($data->lastPage()), $query) : '#' }}"
                        class="pagination-btn {{ $data->hasMorePages() ? '' : 'bg-primary/20 cursor-not-allowed' }}"
                        {{ $data->hasMorePages() ? '' : 'aria-disabled=true' }}>
                        <i class="ph ph-caret-right text-lg"></i>
                    </a>
                </li>
            </ul>
        </div>
    @endif
</div>
@if ($enableCheckbox)
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const selectAllCheckbox = document.querySelector('.select-all-checkbox');
                const rowCheckboxes = document.querySelectorAll('.table-row-checkbox');

                if (selectAllCheckbox) {
                    selectAllCheckbox.addEventListener('change', function() {
                        const isChecked = this.checked;
                        rowCheckboxes.forEach(checkbox => {
                            if (!checkbox.disabled) {
                                checkbox.checked = isChecked;
                            }
                        });
                    });

                    // Optional: Update "select all" checkbox state if individual checkboxes changes
                    rowCheckboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', function() {
                            const allEnabledCheckboxes = Array.from(rowCheckboxes).filter(cb => !cb
                                .disabled);
                            const allChecked = allEnabledCheckboxes.every(cb => cb.checked);
                            const someChecked = allEnabledCheckboxes.some(cb => cb.checked);

                            selectAllCheckbox.checked = allChecked;
                            selectAllCheckbox.indeterminate = someChecked && !allChecked;
                        });
                    });
                }
            });
        </script>
    @endpush
@endif
