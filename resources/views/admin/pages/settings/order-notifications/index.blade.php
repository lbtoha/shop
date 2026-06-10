<x-admin-app-layout>
    @php
        $sms = $settings['sms'];
        $wa = $settings['whatsapp'];
        $msg = $settings['messages'];
        $smsParamsText = collect($sms['params'] ?? [])->map(fn ($v, $k) => $k.'='.$v)->implode("\n");
    @endphp

    <div class="white-box">
        <x-admin::page-header title="{{ __('Order Notifications') }}" :buttons="$buttons" :isFilterable="false" />

        <form action="{{ route('admin.settings.order-notifications.store') }}" method="POST" class="form-submit-edit">
            @csrf

            {{-- Email --}}
            <div class="border border-neutral-30 dark:border-neutral-600 rounded-lg p-4 mb-5">
                <div class="flex items-center justify-between gap-3 mb-1">
                    <div>
                        <p class="m-text font-medium">{{ __('Email Notifications') }}</p>
                        <p class="text-xs">{{ __('Send order emails to customers (uses the saved email templates).') }}</p>
                    </div>
                    <x-admin::switch name="email_enabled" id="email_enabled" :value="$settings['email_enabled'] ? 1 : 0" :types="[['label' => __('Off'), 'value' => 0], ['label' => __('On'), 'value' => 1]]" />
                </div>
            </div>

            {{-- SMS gateway --}}
            <div class="border border-neutral-30 dark:border-neutral-600 rounded-lg p-4 mb-5">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <p class="m-text font-medium">{{ __('SMS Gateway') }}</p>
                        <p class="text-xs">{{ __('Generic HTTP SMS API. Use {to} and {message} placeholders in the parameters.') }}</p>
                    </div>
                    <x-admin::switch name="sms_enabled" id="sms_enabled" :value="!empty($sms['is_enabled']) ? 1 : 0" :types="[['label' => __('Off'), 'value' => 0], ['label' => __('On'), 'value' => 1]]" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-admin::label for="sms_method">{{ __('Method') }}</x-admin::label>
                        <x-admin::select-option name="sms_method" id="sms_method">
                            <option value="GET" @selected(($sms['method'] ?? 'GET') === 'GET')>GET</option>
                            <option value="POST" @selected(($sms['method'] ?? '') === 'POST')>POST</option>
                        </x-admin::select-option>
                    </div>
                    <x-admin::text-input-group name="sms_url" label="API URL" :value="$sms['url'] ?? ''" placeholder="https://api.provider.com/sendsms" />
                    <div class="md:col-span-2">
                        <x-admin::label for="sms_params">{{ __('Parameters (one key=value per line)') }}</x-admin::label>
                        <textarea name="sms_params" id="sms_params" rows="5"
                            class="w-full rounded-md border border-neutral-30 dark:border-neutral-600 bg-transparent p-3 text-sm"
                            placeholder="api_key=YOUR_KEY&#10;senderid=8809...&#10;number={to}&#10;message={message}">{{ $smsParamsText }}</textarea>
                    </div>
                </div>
            </div>

            {{-- WhatsApp Cloud --}}
            <div class="border border-neutral-30 dark:border-neutral-600 rounded-lg p-4 mb-5">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <p class="m-text font-medium">{{ __('WhatsApp (Cloud API)') }}</p>
                        <p class="text-xs">{{ __('Auto-send via Meta WhatsApp Cloud API. Requires a business token + phone number ID.') }}</p>
                    </div>
                    <x-admin::switch name="wa_enabled" id="wa_enabled" :value="!empty($wa['is_enabled']) ? 1 : 0" :types="[['label' => __('Off'), 'value' => 0], ['label' => __('On'), 'value' => 1]]" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-admin::text-input-group name="wa_phone_number_id" label="Phone Number ID" :value="$wa['phone_number_id'] ?? ''" placeholder="1234567890" />
                    <x-admin::text-input-group name="wa_api_version" label="API Version" :value="$wa['api_version'] ?? 'v21.0'" placeholder="v21.0" />
                    <div class="md:col-span-2">
                        <x-admin::label for="wa_token">{{ __('Access Token') }}</x-admin::label>
                        <textarea name="wa_token" id="wa_token" rows="2"
                            class="w-full rounded-md border border-neutral-30 dark:border-neutral-600 bg-transparent p-3 text-sm"
                            placeholder="EAAB...">{{ $wa['token'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Message templates --}}
            <div class="border border-neutral-30 dark:border-neutral-600 rounded-lg p-4 mb-5">
                <p class="m-text font-medium mb-1">{{ __('Message Templates (SMS / WhatsApp)') }}</p>
                <p class="text-xs mb-4">{{ __('Placeholders: {name} {order} {total} {status} {site}') }}</p>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <x-admin::label for="msg_placed">{{ __('Order Placed') }}</x-admin::label>
                        <textarea name="msg_placed" id="msg_placed" rows="2"
                            class="w-full rounded-md border border-neutral-30 dark:border-neutral-600 bg-transparent p-3 text-sm"
                            placeholder="Hi {name}, your order {order} ({total}) has been placed. Thank you!">{{ $msg['placed'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <x-admin::label for="msg_status">{{ __('Status Updated') }}</x-admin::label>
                        <textarea name="msg_status" id="msg_status" rows="2"
                            class="w-full rounded-md border border-neutral-30 dark:border-neutral-600 bg-transparent p-3 text-sm"
                            placeholder="Hi {name}, your order {order} status is now: {status}.">{{ $msg['status'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end">
                <x-admin::primary-button type="submit">{{ __('Save') }}</x-admin::primary-button>
            </div>
        </form>
    </div>
</x-admin-app-layout>
