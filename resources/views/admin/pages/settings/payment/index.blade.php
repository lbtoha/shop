<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Payment Settings') }}" :buttons="$buttons" :isFilterable="false" />

        <form action="{{ route('admin.settings.payment.store') }}" method="POST" class="form-submit-edit">
            @csrf

            <div class="flex items-center gap-3 mb-6">
                <i class="ph ph-credit-card text-2xl text-primary"></i>
                <div>
                    <h3 class="font-semibold text-lg">{{ __('SSLCommerz') }}</h3>
                    <p class="text-sm text-neutral-500">{{ __('Accept online payments in BDT (৳) alongside Cash on Delivery.') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 xxl:gap-6">
                <div class="input-group">
                    <x-admin::label for="sslcommerz_enabled">{{ __('Enable SSLCommerz') }}</x-admin::label>
                    <x-admin::switch name="sslcommerz_enabled" id="sslcommerz_enabled"
                        :value="(int) $settings['sslcommerz_enabled']"
                        :types="[['label' => __('Disabled'), 'value' => 0], ['label' => __('Enabled'), 'value' => 1]]" />
                </div>

                <div class="input-group">
                    <x-admin::label for="sslcommerz_test_mode">{{ __('Sandbox / Test Mode') }}</x-admin::label>
                    <x-admin::switch name="sslcommerz_test_mode" id="sslcommerz_test_mode"
                        :value="(int) $settings['sslcommerz_test_mode']"
                        :types="[['label' => __('Live'), 'value' => 0], ['label' => __('Sandbox'), 'value' => 1]]" />
                </div>

                <x-admin::text-input-group name="sslcommerz_store_id" :value="$settings['sslcommerz_store_id']"
                    label="Store ID" placeholder="testbox" />

                <x-admin::text-input-group name="sslcommerz_store_password" :value="$settings['sslcommerz_store_password']"
                    label="Store Password" placeholder="••••••••" />
            </div>

            <div class="mt-6">
                <x-admin::label for="sslcommerz_logo">{{ __('Payment Logo') }}</x-admin::label>
                <div class="flex items-end gap-4">
                    <div class="flex-1">
                        <x-admin::file-uploader name="sslcommerz_logo" id="sslcommerz_logo"
                            :value="$settings['sslcommerz_logo']" />
                    </div>
                    <div class="shrink-0 text-center">
                        <span class="text-xs text-neutral-500 block mb-1">{{ __('Current') }}</span>
                        <img src="{{ $settings['sslcommerz_logo'] ?: $settings['sslcommerz_default_logo'] }}"
                            alt="SSLCommerz" class="h-10 w-auto border border-neutral-200 rounded bg-white p-1" />
                    </div>
                </div>
                <p class="text-xs text-neutral-500 mt-2">
                    {{ __('Shown beside the "Pay Online" option at checkout. Leave empty to use the default SSLCommerz logo.') }}
                </p>
            </div>

            <p class="text-sm text-neutral-500 mt-3">
                {{ __('When enabled, customers can choose "Pay Online (SSLCommerz)" at checkout. Use the Store Password (API password), not your merchant panel login. Keep Sandbox on while testing.') }}
            </p>

            <div class="flex items-center justify-end mt-4">
                <x-admin::primary-button type="submit">
                    {{ __('Save') }}
                </x-admin::primary-button>
            </div>
        </form>
    </div>
</x-admin-app-layout>
