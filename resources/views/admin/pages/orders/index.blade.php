<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Orders') }}" :tab_buttons="$tab_buttons" :isFilterable="true" />
        <x-admin::table :columns="$columns" :data="$orders" />
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
