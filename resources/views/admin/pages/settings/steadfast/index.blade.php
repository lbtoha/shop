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

            <div class="flex items-center justify-end mt-4">
                <x-admin::primary-button type="submit">
                    {{ __('Save') }}
                </x-admin::primary-button>
            </div>
        </form>
    </div>
</x-admin-app-layout>
