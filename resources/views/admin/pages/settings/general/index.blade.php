<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('General Settings') }}" :isFilterable="false" :buttons="$buttons" />
        <form class="user-information-update" method="POST" action="{{ route('admin.settings.app.info.update') }}" enctype="multipart/form-data">
            @csrf
            <div class="flex flex-col gap-4">
                <div class="border border-neutral-30 dark:border-neutral-700 p-4 rounded">
                    <h6 class="h6 mb-4">{{ __('Company Information') }}</h6>
                    <div class="grid grid-cols-2 gap-4 xl:gap-6">
                        <div class="col-span-2 md:col-span-1">
                            <x-admin::text-input-group name="company_name"
                                value="{{ $settings['company_info']['name'] }}" label="Company Name" />
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <x-admin::text-input-group name="company_email"
                                value="{{ $settings['company_info']['email'] }}" label="Company Email" />
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <x-admin::phone-number-input-group id="company_phone"
                                value="{{ $settings['company_info']['phone'] }}" label="{{ __('Company Phone') }}" />
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <x-admin::text-input-group id="company_website" type="text" name="company_website"
                                value="{{ $settings['company_info']['website'] }}" label="Company Website" />
                        </div>
                    </div>
                </div>
                <div class="border border-neutral-30 dark:border-neutral-700 p-4 rounded">
                    <h6 class="h6 mb-4">{{ __('Timezone Settings') }}</h6>
                    <div class="grid grid-cols-2 gap-4 xl:gap-6">
                        <div class="col-span-2 md:col-span-1">
                            <x-admin::select-option id="timezone" name="timezone">
                                @foreach (timezone_identifiers_list() as $timezone)
                                    <option value="{{ $timezone }}"
                                        {{ $settings['timezone'] == $timezone ? 'selected' : '' }}>{{ $timezone }}
                                    </option>
                                @endforeach
                            </x-admin::select-option>
                        </div>
                    </div>
                </div>
                <div class="border border-neutral-30 dark:border-neutral-700 p-4 rounded">
                    <h6 class="h6 mb-4">{{ __('Client URL Settings (It will be your main domain or frontend url)') }}
                    </h6>
                    <div class="grid grid-cols-2 gap-4 xl:gap-6">
                        <div class="col-span-2 md:col-span-1">
                            <x-admin::text-input-group name="frontend_url"
                                value="{{ $settings['frontend_url'] ?? 'frontend_url' }}"
                                label="Frontend URL (without trailing slash)" />
                        </div>
                    </div>
                </div>
                <div class="border border-neutral-30 dark:border-neutral-700 p-4 rounded">
                    <h6 class="h6 mb-4">{{ __('Address') }}</h6>
                    <div class="grid grid-cols-2 gap-4 xl:gap-6">
                        <div class="col-span-2 md:col-span-1">
                            <x-admin::text-input-group name="address[country]"
                                value="{{ $settings['address']['country'] }}" label="Address Country" />
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <x-admin::text-input-group name="address[city]" value="{{ $settings['address']['city'] }}"
                                label="Address City" />
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <x-admin::text-input-group name="address[state]"
                                value="{{ $settings['address']['state'] ?? '' }}" label="Address State" />
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <x-admin::text-input-group name="address[postal_code]"
                                value="{{ $settings['address']['postal_code'] ?? '' }}" label="Address Postal_code" />
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <x-admin::text-input-group name="address[address]"
                                value="{{ $settings['address']['address'] ?? '' }}" label="Address" />
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <x-admin::text-input-group name="address[location]"
                                value="{{ $settings['address']['location'] ?? '' }}" label="Location" />
                        </div>
                    </div>
                </div>
                <div class="border border-neutral-30 dark:border-neutral-700 p-4 rounded">
                    <h6 class="h6 mb-4">{{ __('Theme') }}</h6>
                    <div class="grid grid-cols-2 gap-4 xl:gap-6">
                        <div class="col-span-2 md:col-span-1 ">
                            <x-admin::label for="primary_color">
                                {{ __('Primary Color') }}
                            </x-admin::label>
                            <div class="flex items-center gap-4">
                                <input name="primary_color" type="color"
                                    value="{{ $settings['theme']['primary_color'] }}" label="Primary Color" />
                                <span>{{ $settings['theme']['primary_color'] }}</span>
                            </div>
                        </div>
                        <div class="col-span-2 md:col-span-1 ">
                            <x-admin::label for="secondary_color">
                                {{ __('Secondary Color') }}
                            </x-admin::label>
                            <div class="flex items-center gap-4">
                                <input name="secondary_color" type="color"
                                    value="{{ $settings['theme']['secondary_color'] }}" label="Secondary Color" />
                                <span>{{ $settings['theme']['secondary_color'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border border-neutral-30 dark:border-neutral-700 p-4 rounded">
                    <h6 class="h6 mb-4">{{ __('Otp Settings') }}</h6>
                    <div class="grid grid-cols-2 gap-4 xl:gap-6">
                        <div class="col-span-2">
                            <x-admin::text-input-group name="otp_duration" type="number"
                                value="{{ $settings['otp']['expire_time'] }}" label="Otp Duration (in minutes)" />
                        </div>
                        @php
                            $range = $settings['otp']['digit_range'];
                            $first = $range[0];
                            $last = $range[1];
                        @endphp
                        <x-admin::text-input-group name="otp_range[0]" type="number" value="{{ $first }}"
                            label="Starting Range" />
                        <x-admin::text-input-group name="otp_range[1]" type="number" value="{{ $last }}"
                            label="Ending Range" />
                    </div>
                </div>
                <div class="border border-neutral-30 dark:border-neutral-700 p-4 rounded">
                    <h6 class="h6 mb-4">{{ __('Referral Settings & Footer') }}</h6>
                    <div class="grid grid-cols-2 gap-4 xl:gap-6">
                        <div class="col-span-2 md:col-span-1">
                            <x-admin::text-input-group name="referral_joining_fee"
                                value="{{ $settings['referral']['joining'] }}" label="Joining Fee" />
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <x-admin::text-input-group name="footer_text" value="{{ $settings['footer_text'] }}"
                                label="Footer Text" />
                        </div>
                    </div>
                </div>
                <div class="border border-neutral-30 dark:border-neutral-700 p-4 rounded">
                    <h6 class="h6 mb-4">{{ __('Mobile App Settings') }}</h6>
                    <div class="grid grid-cols-2 gap-4 xl:gap-6">
                        <div class="col-span-2">
                            <x-admin::text-input-group name="mobile_app_key"
                                value="{{ $settings['mobile_app_key'] ?? '' }}" label="Mobile App Key" />
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <x-admin::text-input-group name="android_link"
                                value="{{ $settings['mobile_app']['android']['link'] ?? '' }}"
                                label="Android App Link" />
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <x-admin::text-input-group name="ios_link"
                                value="{{ $settings['mobile_app']['ios']['link'] ?? '' }}" label="iOS App Link" />
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <x-admin::label for="android_app_file">
                                {{ __('Android App File (APK)') }}
                            </x-admin::label>
                            <input type="file" name="android_app_file"
                                class="w-full border border-neutral-30 dark:border-neutral-700 rounded p-2 text-sm text-neutral-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded file:border-0
                                file:text-sm file:font-semibold
                                file:bg-primary file:text-white
                                hover:file:bg-primary/90">
                            @if (isset($settings['mobile_app']['android']['link']))
                                <div class="mt-2 text-xs">
                                    Current: <a href="{{ $settings['mobile_app']['android']['link'] }}" target="_blank"
                                        class="text-primary truncate block max-w-xs">{{ $settings['mobile_app']['android']['link'] }}</a>
                                </div>
                            @endif
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <x-admin::label for="ios_app_file">
                                {{ __('iOS App File (IPA)') }}
                            </x-admin::label>
                            <input type="file" name="ios_app_file"
                                class="w-full border border-neutral-30 dark:border-neutral-700 rounded p-2 text-sm text-neutral-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded file:border-0
                                file:text-sm file:font-semibold
                                file:bg-primary file:text-white
                                hover:file:bg-primary/90">
                            @if (isset($settings['mobile_app']['ios']['link']))
                                <div class="mt-2 text-xs">
                                    Current: <a href="{{ $settings['mobile_app']['ios']['link'] }}" target="_blank"
                                        class="text-primary truncate block max-w-xs">{{ $settings['mobile_app']['ios']['link'] }}</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="border border-neutral-30 dark:border-neutral-700 p-4 rounded">
                    <h6 class="h6 mb-4">{{ __('Frontend Auth Sidebar Image') }}</h6>
                    <div class="grid grid-cols-2 gap-4 xl:gap-6">
                        <div class="input-group">
                            <x-admin::file-uploader id="auth_left_sidebar_image" :value="$settings['auth_left_sidebar_image']" :image_preview="true"
                                name="auth_left_sidebar_image" />
                            <x-admin::input-error name="auth_left_sidebar_image" />
                        </div>
                    </div>
                </div>
                <div class="col-span-2 flex gap-4">
                    <x-admin::primary-button type="submit" class="btn btn-primary">
                        {{ __('Save') }}
                    </x-admin::primary-button>
                </div>
            </div>
        </form>
    </div>
    @push('scripts')
        @vite('resources/admin/js/settings/general.js')
    @endpush
</x-admin-app-layout>
