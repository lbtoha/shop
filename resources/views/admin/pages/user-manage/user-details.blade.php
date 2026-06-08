<x-admin-app-layout>
    <div class="flex justify-between items-center gap-3 flex-wrap mb-6">
        <p class="l-text font-medium">{{ __('User Details') }}</p>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.signin', $user) }}" class="btn-primary !py-2"><i
                    class="ph ph-sign-in text-lg"></i>{{ __('Sign in as User') }}</a>
            <a href="{{ route('admin.users.ban', $user) }}"
                class="btn-primary !py-2  {{ __($user->status == \App\Enums\UserStatusEnum::BANNED ? 'bg-green-500 border-green-700' : 'bg-yellow-700 border-yellow-700') }}"><i
                    class="ph ph-x-circle text-lg"></i>{{ __($user->status == \App\Enums\UserStatusEnum::BANNED ? __('Active User') : 'Ban User') }}</a>
        </div>
    </div>
    <div class="grid grid-cols-12 gap-4 xxl:gap-6 overflow-x-hidden">
        <div class="col-span-12 space-y-4 xxl:space-y-6">
            <form action="{{ route('admin.users.update', $user) }}" method="POST"
                class="user-information-update white-box">
                @csrf
                @method('PUT')
                <p class="m-text font-medium mb-6">{{ __('Information of') }} {{ $user->full_name }}</p>
                <div class="grid grid-cols-4 gap-4 xxl:gap-6">
                    <div class="col-span-4 md:col-span-2">
                        <x-admin::text-input-group id="name" :value="$user->first_name" name="first_name"
                            label="First Name" />
                    </div>
                    <div class="col-span-4 md:col-span-2">
                        <x-admin::text-input-group :value="$user->last_name" name="last_name" label="Last Name" />
                    </div>
                    <div class="col-span-4 md:col-span-2">
                        <x-admin::text-input-group :value="protectOnDemo($user->email)" name="email" label="Email" />
                    </div>
                    <div class="col-span-4 md:col-span-2">
                        <x-admin::phone-number-input-group :value="protectOnDemo($user->phone)" label="Phone" />
                    </div>
                    <div class="col-span-4">
                        <x-admin::textarea-group :value="$user->address" name="address" label="Address" />
                    </div>
                    <div class="col-span-4 lg:col-span-2 3xl:col-span-1">
                        <x-admin::label for="email_verified_at">{{ __('Email Verified') }}</x-admin::label>
                        <x-admin::switch label="Email Verified" name="email_verified_at"
                            value="{{ $user->email_verified_at ? 1 : 0 }}" :types="[['label' => __('No'), 'value' => 0], ['label' => __('Yes'), 'value' => 1]]" />
                    </div>
                    <div class="col-span-4 lg:col-span-2 3xl:col-span-1">
                        <x-admin::label for="email_verified_at">{{ __('Mobile Verification') }}</x-admin::label>
                        <x-admin::switch name="phone_verified_at" value="{{ $user->phone_verified_at ? 1 : 0 }}"
                            :types="[['label' => 'No', 'value' => 0], ['label' => 'Yes', 'value' => 1]]" />
                    </div>
                    <div class="col-span-4 lg:col-span-2 3xl:col-span-1">
                        <x-admin::label for="is_2fa_enabled">{{ __('2FA Verification') }}</x-admin::label>
                        <x-admin::switch label="2FA Verification" name="is_2fa_enabled"
                            value="{{ $user->is_2fa_enabled ? 1 : 0 }}" :types="[
                                ['label' => __('Disable'), 'value' => 0],
                                ['label' => __('Enable'), 'value' => 1],
                            ]" />
                    </div>
                    <div class="col-span-4">
                        <x-admin::primary-button class="w-full" type="submit">
                            {{ __('Save') }}
                        </x-admin::primary-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @push('scripts')
        @vite('resources/admin/js/manage-user/user-details.js')
    @endpush
</x-admin-app-layout>
