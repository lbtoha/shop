<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Steadfast Courier') }}" :buttons="$buttons" :isFilterable="false" />

        @if (!is_null($balance))
            <div class="flex items-center gap-2 mb-4 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 px-4 py-3">
                <i class="ph ph-wallet text-xl text-emerald-600"></i>
                <span class="s-text">{{ __('Current COD balance') }}: <strong>{{ amountWithSymbol($balance) }}</strong></span>
            </div>
        @endif

        <form action="{{ route('admin.settings.steadfast.store') }}" method="POST" class="form-submit-edit">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 xxl:gap-6">
                <div class="input-group">
                    <x-admin::label for="steadfast_enabled">{{ __('Enable Steadfast Courier') }}</x-admin::label>
                    <x-admin::switch name="steadfast_enabled" id="steadfast_enabled" :value="isset($settings['steadfast_enabled']) ? $settings['steadfast_enabled'] : 0" :types="[['label' => __('Disabled'), 'value' => 0], ['label' => __('Enabled'), 'value' => 1]]" />
                </div>

                <x-admin::text-input-group name="steadfast_base_url" :value="$settings['steadfast_base_url']"
                    label="API Base URL" placeholder="https://portal.packzy.com/api/v1" :required="true" />

                <x-admin::text-input-group name="steadfast_api_key" :value="$settings['steadfast_api_key']"
                    label="API Key" placeholder="{{ __('Your Steadfast Api-Key') }}" />

                <x-admin::text-input-group name="steadfast_secret_key" :value="$settings['steadfast_secret_key']"
                    label="Secret Key" placeholder="{{ __('Your Steadfast Secret-Key') }}" />

                <div class="input-group">
                    <x-admin::label for="steadfast_auto_send">{{ __('Auto-dispatch on Status Change') }}</x-admin::label>
                    <x-admin::switch name="steadfast_auto_send" id="steadfast_auto_send" :value="isset($settings['steadfast_auto_send']) ? $settings['steadfast_auto_send'] : 0" :types="[['label' => __('Disabled'), 'value' => 0], ['label' => __('Enabled'), 'value' => 1]]" />
                </div>

                <div class="input-group">
                    <x-admin::label for="steadfast_auto_send_status">{{ __('Trigger Status') }}</x-admin::label>
                    <x-admin::select-option name="steadfast_auto_send_status" id="steadfast_auto_send_status">
                        @foreach (\App\Enums\OrderStatusEnum::cases() as $case)
                            <option value="{{ $case->value }}" @selected($settings['steadfast_auto_send_status'] === $case->value)>{{ __($case->label()) }}</option>
                        @endforeach
                    </x-admin::select-option>
                </div>
            </div>

            <p class="text-sm text-neutral-500 mt-3">
                {{ __('Find your Api-Key and Secret-Key in the Steadfast merchant portal under API settings. When enabled, you can dispatch any order to Steadfast as a COD consignment from its detail page.') }}
            </p>

            <p class="text-sm text-neutral-500 mt-3">
                {{ __('With auto-dispatch on, an order is sent to Steadfast automatically the first time it reaches the trigger status (e.g. Processing). Orders already dispatched are never sent twice.') }}
            </p>

            <div id="steadfast-test-result" class="hidden mt-4 text-sm rounded-lg px-3 py-2"></div>

            <div class="flex items-center justify-end gap-3 mt-4">
                <button type="button" id="steadfast-test-btn"
                    class="inline-flex items-center gap-2 border border-neutral-200 dark:border-neutral-600 hover:bg-neutral-50 dark:hover:bg-neutral-800 text-neutral-700 dark:text-neutral-200 font-semibold py-2.5 px-4 rounded-md transition">
                    <i class="ph ph-plugs-connected"></i>
                    <span id="steadfast-test-text">{{ __('Test Connection') }}</span>
                </button>
                <x-admin::primary-button type="submit">
                    {{ __('Save') }}
                </x-admin::primary-button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            (function () {
                var btn = document.getElementById('steadfast-test-btn');
                if (!btn) return;
                var text = document.getElementById('steadfast-test-text');
                var box = document.getElementById('steadfast-test-result');
                var endpoint = @json(route('admin.settings.steadfast.test'));
                var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                function show(msg, ok) {
                    box.textContent = msg;
                    box.className = 'mt-4 text-sm rounded-lg px-3 py-2 ' +
                        (ok ? 'bg-success/10 text-success border border-success/20'
                            : 'bg-error/10 text-error border border-error/20');
                    box.classList.remove('hidden');
                }

                btn.addEventListener('click', function () {
                    box.classList.add('hidden');
                    btn.disabled = true;
                    text.textContent = @json(__('Testing…'));

                    var fd = new FormData();
                    fd.append('steadfast_api_key', document.querySelector('[name="steadfast_api_key"]').value);
                    fd.append('steadfast_secret_key', document.querySelector('[name="steadfast_secret_key"]').value);
                    fd.append('steadfast_base_url', document.querySelector('[name="steadfast_base_url"]').value);

                    fetch(endpoint, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                        body: fd,
                    })
                    .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, body: j }; }); })
                    .then(function (res) { show(res.body.message || (res.ok ? 'OK' : 'Failed'), res.ok); })
                    .catch(function () { show(@json(__('Network error. Please try again.')), false); })
                    .finally(function () {
                        btn.disabled = false;
                        text.textContent = @json(__('Test Connection'));
                    });
                });
            })();
        </script>
    @endpush
</x-admin-app-layout>
