<div>
    <div class="white-box">
        <div class="flex justify-between items-center flex-wrap gap-3 mb-6">
            <p class="m-text font-medium">{{ __('Email Notification Settings') }}</p>
            <button data-modal-target="email" class="btn-primary outlined"><i
                    class="ph ph-paper-plane-right"></i>{{ __('Send Test Mail') }}</button>
            <x-admin::modal title="Test Email Send" modalId="email">
                <form action="{{ route('admin.settings.notification.test.email', 'mail') }}" class="form-submit-add"
                    method="POST">
                    @csrf
                    <div class="mb-6 flex flex-col gap-4 xl:gap-6">
                        <x-admin::text-input-group name="send_to" label="Send to" placeholder="Enter email address" />
                        <x-admin::text-input-group name="subject" label="Subject" placeholder="Enter subject" />
                        <x-admin::textarea-group name="message" label="Message" placeholder="Enter message" />
                    </div>
                    <div class="flex gap-3">
                        <x-admin::primary-button type="submit">
                            <span>Send</span>
                        </x-admin::primary-button>
                    </div>
                </form>
            </x-admin::modal>

        </div>
        <form action="{{ route('admin.settings.notification.services.store', 'mail') }}" method="POST"
            class="space-y-4 xl:space-y-6 form-submit-add">
            @csrf
            <div>
                <x-admin::label for="mail_drivers">{{ __('Email Driver') }}</x-admin::label>
                <x-admin::select-option name="mailer" id="email_drivers" value="{{ config('mail.default') }}">
                    <option value="smtp" @if (config('mail.default') == 'smtp') selected @endif>SMTP</option>
                    <option value="mailgun" @if (config('mail.default') == 'mailgun') selected @endif>Mailgun</option>
                </x-admin::select-option>
            </div>

            <div id="smtp-config">
                <p class="mb-4">{{ __('SMTP Configuration') }}</p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 xl:gap-6 mb-4 xl:mb-6">
                    <x-admin::text-input-group name="host" label="{{ __('Host') }}"
                        value="{{ config('mail.mailers.smtp.host') }}" />
                    <x-admin::text-input-group name="port" label="{{ __('Port') }}"
                        value="{{ config('mail.mailers.smtp.port') }}" />
                    <div>
                        <x-admin::label for="encryption">{{ __('Encryption') }}</x-admin::label>
                        <x-admin::select-option name="encryption" class="select-2"
                            value="{{ config('mail.mailers.smtp.encryption') }}">
                            <option value="ssl" @if (config('mail.mailers.smtp.encryption') == 'ssl') selected @endif>SSL</option>
                            <option value="tls" @if (config('mail.mailers.smtp.encryption') == 'tls') selected @endif>Tls</option>
                        </x-admin::select-option>
                        <x-admin::input-error :errors="$errors" name="encryption" />
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 xl:gap-6 mb-4 xl:mb-6">
                    <x-admin::text-input-group name="username" label="{{ __('Username') }}"
                        value="{{ config('mail.mailers.smtp.username') }}" />
                    <x-admin::text-input-group name="password" label="Password"
                        value="{{ config('mail.mailers.smtp.password') }}" />
                </div>
            </div>
            <div id="mailgun-config">
                <p class="mb-4">Mailgun Configuration</p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 xl:gap-6 mb-4 xl:mb-6">
                    <x-admin::text-input-group name="domain" label="Domain"
                        value="{{ config('mail.mailers.mailgun.domain') }}" />
                    <x-admin::text-input-group ame="secret" label="Secret"
                        value="{{ config('mail.mailers.mailgun.secret') }}" />
                    <x-admin::text-input-group name="endpoint" label="Endpoint"
                        value="{{ config('mail.mailers.mailgun.endpoint') }}" />
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 xl:gap-6 mb-4 xl:mb-6">
                <x-admin::text-input-group name="mail_name" label="{{ __('From Name') }}"
                    value="{{ config('mail.from.name') }}" />
                <x-admin::text-input-group name="mail_address" label="{{ __('Form Email') }}"
                    value="{{ config('mail.from.address') }}" />
            </div>
            <div class="w-full">
                <x-admin::primary-button type="submit">
                    <span>{{ __('Save') }}</span>
                </x-admin::primary-button>
            </div>
        </form>
    </div>
</div>
