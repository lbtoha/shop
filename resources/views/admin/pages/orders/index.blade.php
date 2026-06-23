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

        @if ($steadfastEnabled)
            {{-- Bulk action bar — appears once one or more rows are checked --}}
            <div id="orders-bulk-bar" class="hidden items-center justify-between gap-3 mb-4 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 px-4 py-2">
                <span class="s-text"><strong id="orders-bulk-count">0</strong> {{ __('selected') }}</span>
                <button type="button" id="orders-bulk-steadfast" class="flex items-center gap-1 rounded bg-amber-100 text-amber-700 hover:bg-amber-200 px-3 py-1.5 text-xs font-semibold cursor-pointer">
                    <i class="ph ph-truck text-sm"></i>
                    <span>{{ __('Send selected to Steadfast') }}</span>
                </button>
            </div>
        @endif

        <x-admin::table :columns="$columns" :data="$orders" :enableCheckbox="$steadfastEnabled" />
    </div>

    @push('scripts')
        <script type="module">
            $(document).ready(function() {
                $(document).on('change', '.inline-order-status-select', function() {
                    const select = $(this);
                    const action = select.data('action');
                    const value = select.val();

                    const formData = new FormData();
                    formData.append('_method', 'PUT');
                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                    formData.append('status', value);

                    $.ajax({
                        url: action,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (typeof toastSuccess === 'function') {
                                toastSuccess(data?.message || "{{ __('Order status updated successfully') }}");
                            }
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        },
                        error: function(data) {
                            const error = data.responseJSON?.message || "{{ __('Failed to update order status') }}";
                            if (typeof toastError === 'function') {
                                toastError(error);
                            }
                            location.reload();
                        }
                    });
                });

                @if ($steadfastEnabled)
                    // Bulk "Send to Steadfast" — track checked rows, toggle the bar.
                    function steadfastCheckedIds() {
                        return $('.table-row-checkbox:checked').map(function() {
                            return this.value;
                        }).get();
                    }

                    function refreshBulkBar() {
                        const ids = steadfastCheckedIds();
                        const bar = $('#orders-bulk-bar');
                        $('#orders-bulk-count').text(ids.length);
                        if (ids.length > 0) {
                            bar.removeClass('hidden').addClass('flex');
                        } else {
                            bar.addClass('hidden').removeClass('flex');
                        }
                    }

                    $(document).on('change', '.table-row-checkbox, .select-all-checkbox', refreshBulkBar);

                    $('#orders-bulk-steadfast').on('click', function() {
                        const ids = steadfastCheckedIds();
                        if (!ids.length) return;

                        toastConfirm({
                            title: "{{ __('Send to Steadfast?') }}",
                            text: "{{ __('Create Steadfast COD consignments for the selected orders?') }}",
                            action: "{{ route('admin.orders.steadfast.bulk') }}",
                            method: 'POST',
                            data: { ids: ids },
                        });
                    });
                @endif

                $(document).on('change', '.inline-order-payment-select', function() {
                    const select = $(this);
                    const action = select.data('action');
                    const value = select.val();

                    const formData = new FormData();
                    formData.append('_method', 'PUT');
                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                    formData.append('payment_status', value);

                    $.ajax({
                        url: action,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (typeof toastSuccess === 'function') {
                                toastSuccess(data?.message || "{{ __('Payment status updated successfully') }}");
                            }
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        },
                        error: function(data) {
                            const error = data.responseJSON?.message || "{{ __('Failed to update payment status') }}";
                            if (typeof toastError === 'function') {
                                toastError(error);
                            }
                            location.reload();
                        }
                    });
                });
            });
        </script>
    @endpush
</x-admin-app-layout>
