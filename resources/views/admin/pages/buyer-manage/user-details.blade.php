<x-admin-app-layout>
    <div class="flex justify-between items-center gap-3 flex-wrap mb-6">
        <p class="l-text font-medium">{{ __('User Details') }}</p>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.buyers.signin', $buyer) }}" class="btn-primary !py-2"><i
                    class="ph ph-sign-in text-lg"></i>{{ __('Sign in as User') }}</a>
            <a href="{{ route('admin.buyers.ban', $buyer) }}"
                class="btn-primary !py-2  {{ __($buyer->status == \App\Enums\UserStatusEnum::BANNED ? 'bg-green-500 border-green-700' : 'bg-yellow-700 border-yellow-700') }}"><i
                    class="ph ph-x-circle text-lg"></i>{{ __($buyer->status == \App\Enums\UserStatusEnum::BANNED ? __('Active User') : 'Ban User') }}</a>
        </div>
    </div>
    <x-admin::overview-grid :overviews="$overviews" />
    <div class="xxl:gap-6 overflow-x-hidden">
        <form action="{{ route('admin.buyers.update', $buyer) }}" method="POST"
            class="user-information-update white-box">
            @csrf
            @method('PUT')
            <p class="m-text font-medium mb-6">{{ __('Information of') }} {{ $buyer->full_name }}</p>
            <div class="grid grid-cols-4 gap-4 xxl:gap-6">
                <div class="col-span-4 md:col-span-2">
                    <x-admin::text-input-group id="name" :value="$buyer->first_name" name="first_name" label="First Name" />
                </div>
                <div class="col-span-4 md:col-span-2">
                    <x-admin::text-input-group :value="$buyer->last_name" name="last_name" label="Last Name" />
                </div>
                <div class="col-span-4 md:col-span-2">
                    <x-admin::text-input-group :value="$buyer->email" name="email" label="Email" />
                </div>
                <div class="col-span-4 md:col-span-2">
                    <x-admin::phone-number-input-group :value="$buyer->phone" label="Phone" />
                </div>
                <div class="col-span-4">
                    <x-admin::textarea-group :value="$buyer->address" name="address" label="Address" />
                </div>
                <div class="col-span-4 lg:col-span-2 3xl:col-span-1">
                    <x-admin::label for="email_verified_at">{{ __('Email Verified') }}</x-admin::label>
                    <x-admin::switch label="Email Verified" name="email_verified_at"
                        value="{{ $buyer->email_verified_at ? 1 : 0 }}" :types="[['label' => __('No'), 'value' => 0], ['label' => __('Yes'), 'value' => 1]]" />
                </div>
                <div class="col-span-4 lg:col-span-2 3xl:col-span-1">
                    <x-admin::label for="email_verified_at">{{ __('Mobile Verification') }}</x-admin::label>
                    <x-admin::switch name="phone_verified_at" value="{{ $buyer->phone_verified_at ? 1 : 0 }}"
                        :types="[['label' => 'No', 'value' => 0], ['label' => 'Yes', 'value' => 1]]" />
                </div>
                <div class="col-span-4 lg:col-span-2 3xl:col-span-1">
                    <x-admin::label for="is_2fa_enabled">{{ __('2FA Verification') }}</x-admin::label>
                    <x-admin::switch label="2FA Verification" name="is_2fa_enabled"
                        value="{{ $buyer->is_2fa_enabled ? 1 : 0 }}" :types="[['label' => __('Disable'), 'value' => 0], ['label' => __('Enable'), 'value' => 1]]" />
                </div>

                <div class="col-span-4">
                    <x-admin::primary-button class="w-full" type="submit">
                        {{ __('Save') }}
                    </x-admin::primary-button>
                </div>
            </div>
        </form>
    </div>
    @push('scripts')
        @vite('resources/admin/js/manage-user/user-details.js')
    @endpush
</x-admin-app-layout>
