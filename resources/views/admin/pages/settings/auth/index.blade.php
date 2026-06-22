<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Customer Auth Settings') }}" :buttons="$buttons" :isFilterable="false" />

        <form action="{{ route('admin.settings.auth.store') }}" method="POST" class="form-submit-edit">
            @csrf

            <div class="flex items-center gap-3 mb-6">
                <i class="ph ph-user-circle-check text-2xl text-primary"></i>
                <div>
                    <h3 class="font-semibold text-lg">{{ __('Signup Verification') }}</h3>
                    <p class="text-sm text-neutral-500">{{ __('Require new customers to verify their email with a one-time code (OTP) before the account is created.') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 xxl:gap-6">
                <div class="input-group">
                    <x-admin::label for="signup_otp_enabled">{{ __('Require OTP on Signup') }}</x-admin::label>
                    <x-admin::switch name="signup_otp_enabled" id="signup_otp_enabled"
                        :value="(int) $settings['signup_otp_enabled']"
                        :types="[['label' => __('Disabled'), 'value' => 0], ['label' => __('Enabled'), 'value' => 1]]" />
                </div>
            </div>

            <p class="text-sm text-neutral-500 mt-3">
                {{ __('When enabled, customers signing up with an email receive a verification code by email and must enter it to finish registration. Code length and expiry are set under System Settings → General. Note: phone verification needs an SMS gateway (not configured) — customers registering with only a phone are asked to use an email while this is on.') }}
            </p>

            <div class="flex items-center justify-end mt-4">
                <x-admin::primary-button type="submit">
                    {{ __('Save') }}
                </x-admin::primary-button>
            </div>
        </form>
    </div>
</x-admin-app-layout>
