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

                <div class="input-group">
                    <x-admin::label for="whatsapp_enabled">{{ __('WhatsApp Contact Button') }}</x-admin::label>
                    <x-admin::switch name="whatsapp_enabled" id="whatsapp_enabled" :value="isset($settings['whatsapp_enabled']) ? $settings['whatsapp_enabled'] : 0" :types="[['label' => __('Disabled'), 'value' => 0], ['label' => __('Enabled'), 'value' => 1]]" />
                </div>

                <x-admin::text-input-group name="whatsapp_number" :value="$settings['whatsapp_number']"
                    label="WhatsApp Number" placeholder="8801710733329" />

                <div class="input-group">
                    <x-admin::label for="show_product_category">{{ __('Show Category on Product Page') }}</x-admin::label>
                    <x-admin::switch name="show_product_category" id="show_product_category" :value="isset($settings['show_product_category']) ? $settings['show_product_category'] : 1" :types="[['label' => __('Disabled'), 'value' => 0], ['label' => __('Enabled'), 'value' => 1]]" />
                </div>

                <div class="md:col-span-2 my-2 border-t border-gray-100 pt-4">
                    <h3 class="text-sm font-semibold text-neutral-800 uppercase tracking-wider mb-2">{{ __('Company & Social Information') }}</h3>
                </div>

                <x-admin::text-input-group name="company_phone" :value="$settings['company_phone']"
                    label="Company Phone" placeholder="+123 456 789" />

                <x-admin::text-input-group name="facebook_link" :value="$settings['facebook_link']"
                    label="Facebook Page URL" placeholder="https://facebook.com/page" />

                <x-admin::text-input-group name="instagram_link" :value="$settings['instagram_link']"
                    label="Instagram URL" placeholder="https://instagram.com/profile" />

                <x-admin::text-input-group name="youtube_link" :value="$settings['youtube_link']"
                    label="YouTube Channel URL" placeholder="https://youtube.com/channel" />

                <x-admin::text-input-group name="tiktok_link" :value="$settings['tiktok_link']"
                    label="TikTok URL" placeholder="https://tiktok.com/@username" />

                <x-admin::text-input-group name="twitter_link" :value="$settings['twitter_link']"
                    label="Twitter (X) URL" placeholder="https://twitter.com/username" />
            </div>

            <p class="text-sm text-neutral-500 mt-3">
                {{ __('When enabled, a WhatsApp contact button shows on every product page. Use the full international number without "+" (e.g. 8801710733329).') }}
            </p>

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
