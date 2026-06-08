<div>
    <div class="white-box">
        <div class="flex justify-between items-center flex-wrap gap-3 mb-6">
            <p class="m-text font-medium">{{ __('SMS Notification Settings') }}</p>
            <button data-modal-target="sms" class="btn-primary outlined"><i
                    class="ph ph-paper-plane-right"></i>{{ __('Send Test Sms') }}</button>
            <x-admin::modal title="Test Email Send" modalId="sms">
                <form action="{{ route('admin.settings.notification.test.sms') }}" class="form-submit-add" method="POST">
                    @csrf
                    <div class="mb-6 flex flex-col gap-4 xl:gap-6">
                        <x-admin::text-input-group name="send_to" label="Send to" value="{{ old('send_to') }}" />
                        <x-admin::textarea-group name="message" label="Message" />
                    </div>
                    <div class="flex gap-3">
                        <x-admin::primary-button type="submit">
                            <span>{{ __('Send') }}</span>
                        </x-admin::primary-button>
                    </div>
                </form>
            </x-admin::modal>
        </div>
        <form method="POST" action="{{ route('admin.settings.notification.services.store', 'sms') }}"
            class="space-y-4 xl:space-y-6 form-submit-add">
            @csrf
            <div>
                <x-admin::label for="sms_drivers">{{ __('SMS Driver') }}</x-admin::label>
                <x-admin::select-option name="driver" id="sms_drivers">
                    @foreach (config('sms.drivers') as $key => $driver)
                        <option value="{{ $key }}" @if (config('sms.default') == $key) selected @endif>
                            {{ $key }}
                        </option>
                    @endforeach
                </x-admin::select-option>
            </div>
            <div id="twilio-form">
                <p class="mb-4">{{ __('Twilio Configuration') }}</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 xl:gap-6">
                    <x-admin::text-input-group name="account_sid" label="{{ __('Account Sid') }}"
                        value="{{ config('sms.drivers.twilio.account_sid') }}" />
                    <x-admin::text-input-group name="auth_token" label="{{ __('Auth Token') }}"
                        value="{{ config('sms.drivers.twilio.auth_token') }}" />
                    <x-admin::text-input-group name="from" label="{{ __('From') }}"
                        value="{{ config('sms.drivers.twilio.from') }}" />
                </div>
            </div>
            <div id="nexmo-form">
                <p class="mb-4">{{ __('Nexmo Configuration') }}</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 xl:gap-6">
                    <x-admin::text-input-group name="api_key" label="{{ __('API Key') }}"
                        value="{{ config('sms.drivers.nexmo.api_key') }}" />
                    <x-admin::text-input-group name="api_secret" label="{{ __('Auth Token') }}"
                        value="{{ config('sms.drivers.nexmo.api_secret') }}" />
                    <x-admin::text-input-group name="from" label="{{ __('From') }}" placeholder="From"
                        value="{{ config('sms.drivers.nexmo.from') }}" />
                </div>
            </div>
            <div class="flex justify-end gap-4 mt-4">
                <x-admin::primary-button type="submit">
                    <span>{{ __('Save') }}</span>
                </x-admin::primary-button>
            </div>
        </form>
    </div>
</div>
