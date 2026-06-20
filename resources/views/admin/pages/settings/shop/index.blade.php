<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Shop Settings') }}" :buttons="$buttons" :isFilterable="false" />

        <form action="{{ route('admin.settings.shop.store') }}" method="POST" class="form-submit-edit">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 xxl:gap-6">
                <x-admin::text-input-group name="currency_symbol" :value="$settings['currency_symbol']"
                    label="Currency Symbol" placeholder="$" :required="true" />

                <x-admin::text-input-group name="currency_code" :value="$settings['currency_code']"
                    label="Currency Code" placeholder="USD" :required="true" />

                <x-admin::number-input-group name="shipping_cost" :value="$settings['shipping_cost']"
                    label="Flat Shipping Cost" :with_currencySymbol="false" />

                <div class="input-group">
                    <x-admin::label for="show_ratings">{{ __('Show Product Ratings') }}</x-admin::label>
                    <x-admin::switch name="show_ratings" id="show_ratings" :value="isset($settings['show_ratings']) ? $settings['show_ratings'] : 0" :types="[['label' => __('Disabled'), 'value' => 0], ['label' => __('Enabled'), 'value' => 1]]" />
                </div>
            </div>

            <p class="text-sm text-neutral-500 mt-3">
                {{ __('Flat shipping cost is added to every cash-on-delivery order at checkout. Set 0 for free shipping.') }}
            </p>

            <div class="flex items-center justify-end mt-4">
                <x-admin::primary-button type="submit">
                    {{ __('Save') }}
                </x-admin::primary-button>
            </div>
        </form>
    </div>
</x-admin-app-layout>
