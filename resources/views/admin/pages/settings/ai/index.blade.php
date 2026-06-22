<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('AI Settings') }}" :buttons="$buttons" :isFilterable="false" />

        <form action="{{ route('admin.settings.ai.store') }}" method="POST" class="form-submit-edit">
            @csrf

            <div class="flex items-center gap-3 mb-6">
                <i class="ph ph-sparkle text-2xl text-primary"></i>
                <div>
                    <h3 class="font-semibold text-lg">{{ __('AI Virtual Try-On') }}</h3>
                    <p class="text-sm text-neutral-500">{{ __('Let customers upload a photo and preview products on themselves, powered by Google Gemini.') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 xxl:gap-6">
                <div class="input-group">
                    <x-admin::label for="ai_tryon_enabled">{{ __('Enable Virtual Try-On') }}</x-admin::label>
                    <x-admin::switch name="ai_tryon_enabled" id="ai_tryon_enabled"
                        :value="(int) $settings['ai_tryon_enabled']"
                        :types="[['label' => __('Disabled'), 'value' => 0], ['label' => __('Enabled'), 'value' => 1]]" />
                </div>

                <x-admin::text-input-group name="ai_tryon_model" :value="$settings['ai_tryon_model']"
                    label="Gemini Model" placeholder="gemini-3.1-flash-image" />

                <div class="md:col-span-2">
                    <x-admin::text-input-group name="ai_tryon_api_key" :value="$settings['ai_tryon_api_key']"
                        label="Gemini API Key" placeholder="AIza…" />
                </div>
            </div>

            <p class="text-sm text-neutral-500 mt-3">
                {{ __('Get a key from Google AI Studio (aistudio.google.com). When enabled, a "Try it On" button appears on every product page. Image generation is billed per request by Google — keep this off if you are not using it. Customer photos are never stored; generated previews are auto-deleted after 24 hours.') }}
            </p>

            {{-- Abuse protection --}}
            <div class="flex items-center gap-3 mt-8 mb-4 pt-6 border-t border-neutral-200 dark:border-neutral-700">
                <i class="ph ph-shield-check text-2xl text-primary"></i>
                <div>
                    <h3 class="font-semibold text-lg">{{ __('Abuse Protection') }}</h3>
                    <p class="text-sm text-neutral-500">{{ __('Limit how often the (billed) try-on can run, to prevent spam from draining your Google budget.') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 xxl:gap-6">
                <div class="input-group">
                    <x-admin::label for="ai_tryon_login_required">{{ __('Require Login') }}</x-admin::label>
                    <x-admin::switch name="ai_tryon_login_required" id="ai_tryon_login_required"
                        :value="(int) $settings['ai_tryon_login_required']"
                        :types="[['label' => __('Guests allowed'), 'value' => 0], ['label' => __('Login required'), 'value' => 1]]" />
                </div>

                <x-admin::number-input-group name="ai_tryon_daily_global" :value="$settings['ai_tryon_daily_global']"
                    label="Site-wide Daily Limit" :with_currencySymbol="false" />

                <x-admin::number-input-group name="ai_tryon_per_hour" :value="$settings['ai_tryon_per_hour']"
                    label="Per Visitor / Hour" :with_currencySymbol="false" />

                <x-admin::number-input-group name="ai_tryon_per_day" :value="$settings['ai_tryon_per_day']"
                    label="Per Visitor / Day" :with_currencySymbol="false" />
            </div>

            <p class="text-sm text-neutral-500 mt-3">
                {{ __('Per-visitor limits are tracked by account (or IP for guests). The site-wide daily limit is a hard ceiling across all visitors that resets at midnight. A coarse 10/minute throttle also applies automatically.') }}
            </p>

            <div id="ai-test-result" class="hidden mt-4 text-sm rounded-lg px-3 py-2"></div>

            <div class="flex items-center justify-end gap-3 mt-4">
                <button type="button" id="ai-test-btn"
                    class="inline-flex items-center gap-2 border border-neutral-200 dark:border-neutral-600 hover:bg-neutral-50 dark:hover:bg-neutral-800 text-neutral-700 dark:text-neutral-200 font-semibold py-2.5 px-4 rounded-md transition">
                    <i class="ph ph-plugs-connected"></i>
                    <span id="ai-test-text">{{ __('Test Connection') }}</span>
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
                var btn = document.getElementById('ai-test-btn');
                if (!btn) return;
                var text = document.getElementById('ai-test-text');
                var box = document.getElementById('ai-test-result');
                var endpoint = @json(route('admin.settings.ai.test'));
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
                    fd.append('ai_tryon_api_key', document.querySelector('[name="ai_tryon_api_key"]').value);
                    fd.append('ai_tryon_model', document.querySelector('[name="ai_tryon_model"]').value);

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
