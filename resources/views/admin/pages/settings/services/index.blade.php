<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Services') }}" :buttons="$buttons" :isFilterable="false" />
        <form action="{{ route('admin.settings.services.store') }}" method="POST" class="form-submit-edit">
            @csrf
            <div class="flex flex-col gap-4 xxl:gap-6">
                {{-- currency information --}}
                <div class="border border-neutral-30 dark:border-neutral-700 p-4 rounded rounded space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="m-text font-medium">{{ __('Currency Layer Access Key') }}</h4>

                        <button type="button" data-modal-target="help-currency" class="btn-primary outlined">
                            <i class="ph ph-info"></i>{{ __('Help') }}
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-4 xl:gap-6">
                        <div class="col-span-2">
                            <x-admin::text-input name="currencylayer_access_key"
                                value="{{ config('services.currencylayer.access_key') }}" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end mt-4">
                <x-admin::primary-button>
                    {{ __('Save') }}
                </x-admin::primary-button>
            </div>
        </form>
    </div>
    <x-admin::modal title="How to Access the Currencylayer Website and Get an Access Key" modalId="help-currency">
        <div class="font-sans max-w-4xl mx-auto p-6 space-y-8 text-gray-800">
            <p class="text-gray-600">{{ __('This guide explains how to access the Currencylayer website and retrieve your API access key from the dashboard.') }}</p>

            <div class="space-y-4">
                <h4 class="text-gray-900 text-xl font-medium">{{ __('Step 1: Visit the Currencylayer Website') }}</h4>
                <ol class="list-decimal pl-6 text-gray-600">
                    <li>{{ __('Open your web browser.') }}</li>
                    <li>{{ __('Navigate to the official Currencylayer website at') }} <a href="https://currencylayer.com" target="_blank" class="text-blue-500 hover:underline">https://currencylayer.com</a>.</li>
                </ol>
            </div>

            <div class="space-y-4">
                <h4 class="text-gray-900 text-xl font-medium">{{ __('Step 2: Sign Up for an Account') }}</h4>
                <ol class="list-decimal pl-6 text-gray-600">
                    <li>On the Currencylayer homepage, locate the <strong>Sign Up</strong> or <strong>Get Free API Key</strong> button (typically found in the top-right corner or on the main page).</li>
                    <li>{{ __('Click the button to start the registration process.') }}</li>
                    <li>{{ __('Fill out the registration form with your details, including:') }}
                        <ul class="list-disc pl-6 mt-2">
                            <li>{{ __('Name') }}</li>
                            <li>{{ __('Email Address') }}</li>
                            <li>{{ __('Password') }}</li>
                        </ul>
                    </li>
                    <li>Agree to the terms of service and click <strong>Sign Up</strong> or <strong>Create Account</strong>.</li>
                    <li>{{ __('Check your email for a confirmation link from Currency layer and click it to verify your account.') }}</li>
                </ol>
            </div>

            <div class="space-y-4">
                <h4 class="text-gray-900 text-xl font-medium">{{ __('Step 3: Log In to Your Account') }}</h4>
                <ol class="list-decimal pl-6 text-gray-600">
                    <li>{{ __('Return to') }} <a href="https://currencylayer.com" target="_blank" class="text-blue-500 hover:underline">https://currencylayer.com</a>.</li>
                    <li>Click the <strong>Log In</strong> button (usually in the top-right corner).</li>
                    <li>{{ __('Enter your registered email address and password, then click') }} <strong>{{ __('Log In') }}</strong>.</li>
                </ol>
            </div>

            <div class="space-y-4">
                <h4 class="text-gray-900 text-xl font-medium">{{ __('Step 4: Access the Dashboard') }}</h4>
                <ol class="list-decimal pl-6 text-gray-600">
                    <li>Once logged in, you will be directed to your Currency layer account dashboard. If not, look for a <strong>Dashboard</strong> or <strong>Account</strong> link in the navigation menu and click it.</li>
                </ol>
            </div>

            <div class="space-y-4">
                <h4 class="text-gray-900 text-xl font-medium">{{ __('Step 5: Retrieve Your API Access Key') }}</h4>
                <ol class="list-decimal pl-6 text-gray-600">
                    <li>In the dashboard, locate the <strong>API Access Key</strong> section. This is often displayed prominently in:
                        <ul class="list-disc pl-6 mt-2">
                            <li>{{ __('The main dashboard view') }}</li>
                            <li>A <strong>{{ __('3-Step Quickstart Guide') }}</strong></li>
                            <li>An <strong>API Key</strong> or <strong>Account Details</strong> tab</li>
                        </ul>
                    </li>
                    <li>{{ __('Your unique API access key will be a string of alphanumeric characters') }} (e.g., <code class="bg-gray-100 px-1 py-0.5 rounded">1234567890abcdef1234567890abcdef</code>).</li>
                    <li>{{ __('Copy the key and store it securely, as it is required for all API requests.') }}</li>
                    <li>If you need to reset your key, look for a <strong>Reset Access Key</strong> option in the dashboard and follow the prompts.</li>
                </ol>
            </div>

            <div class="space-y-4">
                <h4 class="text-gray-900 text-xl font-medium">{{ __('Additional Notes') }}</h4>
                <ul class="list-disc pl-6 text-gray-600">
                    <li>{{ __('The free plan offers up to 1,000 monthly API requests with hourly updates. Paid plans provide higher quotas and more frequent updates (up to every 60 seconds).') }}</li>
                    <li>{{ __('Ensure your connection uses HTTPS for secure data transfer.') }}</li>
                    <li>{{ __('You can manage your subscription, payment methods, and API usage directly from the dashboard.') }}</li>
                </ul>
            </div>
        </div>
    </x-admin::modal>
</x-admin-app-layout>
